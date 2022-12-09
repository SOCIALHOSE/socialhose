<?php

namespace IndexBundle\Normalizer\Query;

/**
 * Class QueryNormalizer
 *
 * Default implementation of QueryNormalizerInterface.
 *
 * @package IndexBundle\Normalizer\Query
 */
class QueryNormalizer implements QueryNormalizerInterface
{

    /**
     * Normalize raw search query.
     *
     * @param string $query Raw search query.
     *
     * @return string
     */
    public function normalize($query)
    {
        $query = trim($query);
        if ($query === '') {
            return '';
        }

        $tokens = QueryLexer::process($query);
        $tokens = $this->reorder($tokens);

        while (count($tokens) > 1) {
            $tokens = $this->normalizationStep($tokens);
        }

        if (count($tokens) === 0) {
            return '';
        }

        return (string) $tokens[0];
    }

    /**
     * @param Token[] $tokens Array of normalized tokens.
     *
     * @return array
     */
    private function normalizationStep(array $tokens)
    {
        $length = count($tokens);
        $buf = [];

        $normalizeToken = function ($idx) use (&$tokens) {
            return isset($tokens[$idx]) ? $tokens[$idx] : Token::nullToken();
        };

        for ($i = 0; $i < $length;) {
            $first = $tokens[$i];
            //
            // In some case we can try to get element beyond index, so we
            // need to handle this situation.
            //
            $second = $normalizeToken($i + 1);
            $third = $normalizeToken($i + 2);

            if ($third->isBinaryOperator()
                && ! $first->isOperator()
                && ! $second->isOperator()
            ) {
                //
                // Group of tokens.
                // After reordering we can have next elements in stack:
                //
                // x y OR z AND a b OR AND
                //
                // In this section we process `x y OR` and `a b OR` parts of
                // stack and create new token, it may be a single word or a
                // group of words.
                //
                $group = Token::groupToken([$first, $second], $third);
                $buf[] = $this->normalizeToken($group);

                //
                // We jump through next two elements because we already
                // process it.
                //
                $i += 3;
            } elseif ($second->isNot() && ($first->isGroup() || ! $first->isOperator())) {
                $buf[] = Token::negativeGroupToken($first);
                $i += 2;
            } else {
                //
                // Single token, like operator or single word.
                //
                $buf[] = $first;
                ++$i;
            }
        }

        return $buf;
    }

    /**
     * Reorder tokens in Reverse Polish Notation.
     * {@link https://en.wikipedia.org/wiki/Reverse_Polish_notation}
     *
     * @param array|Token[] $tokens Query string tokens.
     *
     * @return Token[]
     */
    private function reorder($tokens)
    {
        $operators = [];
        $result = [];

        foreach ($tokens as $token) {
            switch ($token->getType()) {
                //
                // If current token is word we add it to result stack.
                //
                case Token::TYPE_WORD:
                    $result[] = $this->normalizeToken($token);
                    break;

                //
                // Open bracket we add to operators stack without any over
                // actions.
                //
                case Token::TYPE_OPEN_BRACKET:
                    $operators[] = $token;
                    break;

                //
                // If we got close bracket we should pop all operators that go
                // to the first opening bracket into result stack and remove
                // opening bracket from operators stack.
                //
                case Token::TYPE_CLOSE_BRACKET:
                    /** @var Token $element */
                    while (($element = array_pop($operators))
                        && ! $element->isOpenBracket()) {
                        $result[] = $element;
                    }
                    break;

                //
                // We got one of operators like AND or OR.
                //
                default:
                    $this->addOperation($operators, $result, $token);
            }
        }

        //
        // Push all remain operators into result stack.
        //
        while (($element = array_pop($operators)) !== null) {
            $result[] = $element;
        }

        return $result;
    }

    /**
     * Add given operators token.
     *
     * @param array|Token[] $operators Operation stack.
     * @param array|Token[] $result    Result stack.
     * @param Token         $token     Current token.
     *
     * @return void
     */
    private function addOperation(
        array &$operators,
        array &$result,
        Token $token
    ) {
        while (true) {
            //
            // Get last operator from stack.
            //
            /** @var Token $lastOperatorToken */
            $lastOperatorToken = array_pop($operators);
            if (! $lastOperatorToken) {
                //
                // If we don't have any operators we just push current operator
                // into stack and break loop.
                //
                $operators[] = $token;
                break;
            }

            //
            // If we have operators in stack we should check priorities.
            //
            if ($token->getPriority() > $lastOperatorToken->getPriority()) {
                //
                // If current priority greater than previous we add both of
                // them into operators stack.
                //
                $operators[] = $lastOperatorToken;
                $operators[] = $token;
                break;
            }

            //
            // Otherwise put last operator token into result stack.
            //
            $result[] = $lastOperatorToken;
        }
    }

    /**
     * Normalize given token.
     *
     * @param Token $token A Token instance.
     *
     * @return Token
     */
    private function normalizeToken(Token $token)
    {
        if ($token->isNormalized()) {
            return $token;
        }

        if ($token->isGroup()) {
            $first = $this->normalizeToken($token->getValue()[0]);
            $second = $this->normalizeToken($token->getValue()[1]);

            if ($first->isGroup() || $second->isGroup()) {
                $token = $this
                    ->normalizeGroup($first, $second, $token->getGroupOperator());
            } else {
                $words = $this->sortWords([$first, $second]);

                if (count($words) > 1) {
                    $token = Token::groupToken(
                        $this->sortWords([$first, $second]),
                        $token->getGroupOperator()
                    );
                } else {
                    $token = new Token($first->getType(), $first->getValue());
                }
            }
        } else {
            $token = new Token($token->getType(), strtolower($token->getValue()));
        }

        $token->setNormalized(true);
        return $token;
    }

    /**
     * @param Token $first    A first Token instance.
     * @param Token $second   A second Token instance.
     * @param Token $operator A operator Token instance.
     *
     * @return Token
     */
    private function normalizeGroup(Token $first, Token $second, Token $operator)
    {
        $tokens = [$first, $second];

        if ($operator->isSameType($first->getGroupOperator())
            || $operator->isSameType($second->getGroupOperator())) {
            $tokens = array_merge(
                $first->isGroup() ? $first->getValue() : [ $first ],
                $second->isGroup() ? $second->getValue() : [ $second ]
            );
        }

        return Token::groupToken($this->sortWords($tokens), $operator);
    }

    /**
     * Sort words in ascending order.
     *
     * @param array|Token[] $words Array of tokens.
     *
     * @return array
     */
    private function sortWords($words)
    {
        $buf = [];

        if (count($words) === 1) {
            return $words[0];
        }

        //
        // Create map between token value and token.
        //
        foreach ($words as $word) {
            $buf[(string) $word] = $word;
        }

        //
        // Sort words and create query string for given query part.
        //
        ksort($buf);
        return array_values($buf);
    }
}
