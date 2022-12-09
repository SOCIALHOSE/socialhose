<?php

namespace IndexBundle\Normalizer\Query;

use Doctrine\Common\Lexer\AbstractLexer;

/**
 * Class QueryLexer
 *
 * Lexer for raw query string from user like 'Cat OR (dog and Bird)'.
 *
 * @package IndexBundle\Normalizer\Query
 */
class QueryLexer extends AbstractLexer
{

    /**
     * @param string $queryString Search request query string.
     *
     * @return Token[]
     */
    public static function process($queryString)
    {
        //
        // Normalize query string.
        //
        $queryString = trim(preg_replace('/\s{2,}/', ' ', $queryString));

        $lexer = new static();
        $lexer->setInput($queryString);

        $tokens = self::buildTokenArrayFromLexer($lexer);

        //
        // We may get operators at the beginning and at the end of tokens list
        // and this is because of error in query typed by user, so we should drop
        // this operators.
        //
        while ((count($tokens) > 0) && $tokens[0]->isBinaryOperator()) {
            array_shift($tokens);
        }

        while ((count($tokens) > 0) && $tokens[count($tokens) - 1]->isOperator()) {
            array_pop($tokens);
        }

        return $tokens;
    }

    /**
     * @param QueryLexer $lexer A QueryLexer instance.
     *
     * @return Token[]
     */
    protected static function buildTokenArrayFromLexer(QueryLexer $lexer)
    {
        /** @var Token[] $tokens */
        $tokens = [];
        while ($lexer->moveNext() && $lexer->lookahead !== null) {
            $current = Token::fromLexerToken($lexer->token);
            $next = Token::fromLexerToken($lexer->lookahead);

            if ($current->isNot() && $next->isNull()) {
                //
                // We got 'NOT' at the end of query, this is invalid query so we
                // don't add this 'NOT' into result set.
                //
                continue;
            }

            if (($current->isWord() || $current->isCloseBracket())
                && ($next->isWord() || $next->isOpenBracket())) {
                //
                // We got two words which go one after another or words after
                // open bracket. In that case we need to add 'OR' token between
                // them because spaces between words are the same as 'OR' tokens.
                //
                $tokens[] = new Token(Token::TYPE_OR, 'OR');
            }

            $tokens[] = $next;
        }

        return $tokens;
    }

    /**
     * Lexical catchable patterns.
     *
     * @return array
     */
    protected function getCatchablePatterns()
    {
        //
        // We should catch all parentheses, words and quotes.
        //
        return [
            '\(|\)',
            '"[^"]+"[+~\d.^]*',
            '[\w^~+*?.]+',
        ];
    }

    /**
     * Lexical non-catchable patterns.
     *
     * @return array
     */
    protected function getNonCatchablePatterns()
    {
        //
        // We don't want to catch spaces.
        //
        return [ '\s+' ];
    }

    /**
     * Retrieve token type. Also processes the token value if necessary.
     *
     * @param string $value Token value.
     *
     * @return integer
     */
    protected function getType(&$value)
    {
        switch ($value) {
            case 'OR':
                return Token::TYPE_OR;

            case 'AND':
                return Token::TYPE_AND;

            case 'NOT':
                return Token::TYPE_NOT;

            case '(':
                return Token::TYPE_OPEN_BRACKET;

            case ')':
                return Token::TYPE_CLOSE_BRACKET;
        }

        return Token::TYPE_WORD;
    }
}
