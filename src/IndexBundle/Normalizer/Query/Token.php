<?php

namespace IndexBundle\Normalizer\Query;

/**
 * Class Token
 * @package IndexBundle\Normalizer\Query
 */
class Token
{

    /**
     * Token contains simple word.
     */
    const TYPE_WORD = 0;

    /**
     * Token is AND operator between two words/groups.
     */
    const TYPE_AND = 1;

    /**
     * Token is OR operator between two words/groups.
     */
    const TYPE_OR = 2;

    /**
     * Token is NOT operator before word/group.
     */
    const TYPE_NOT = 3;

    /**
     * Token is open bracket - start of group.
     */
    const TYPE_OPEN_BRACKET = 10;

    /**
     * Token is close bracket - end of group.
     */
    const TYPE_CLOSE_BRACKET = 11;

    /**
     * Token is group.
     */
    const TYPE_GROUP = 100;

    /**
     * Token group with not above it.
     */
    const TYPE_NEGATIVE_GROUP = 101;

    /**
     * Null token.
     */
    const TYPE_NULL = -1;

    /**
     * Priority table of available token types.
     *
     * @var array
     */
    static private $operationPriorities = [
        self::TYPE_NOT => 5,
        self::TYPE_AND => 4,
        self::TYPE_OR => 3,
        self::TYPE_OPEN_BRACKET => 2,
        self::TYPE_CLOSE_BRACKET => 2,
        self::TYPE_WORD => 0,
        self::TYPE_GROUP => 0,
        self::TYPE_NEGATIVE_GROUP => 0,
    ];

    /**
     * @var string|Token[]
     */
    private $value;

    /**
     * @var integer
     */
    private $type;

    /**
     * @var Token
     */
    private $groupOperator;

    /**
     * @var boolean
     */
    private $normalized = false;

    /**
     * @param integer        $type  Token type.
     * @param string|Token[] $value Token value, if null generate from type.
     */
    public function __construct($type, $value = null)
    {
        $this->value = $value;
        $this->type = $type;
        if ($type !== self::TYPE_NULL) {
            $this->groupOperator = self::nullToken();
        }
    }

    /**
     * Convert token to query string.
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->isGroup()) {
            $length = count($this->value);
            $result = [];

            for ($i = 0; $i < $length; ++$i) {
                $result[] = (string) $this->value[$i];
                if ($i < $length - 1) {
                    $result[] = $this->groupOperator->getValue();
                }
            }

            $result = '('. implode(' ', $result) .')';
        } else {
            //
            // Make proper handling for negative group too.
            //
            $result = '';
            if ($this->isNegativeGroup()) {
                $result = 'NOT ';
            }
            $result .= (string) $this->value;
        }

        return $result;
    }

    /**
     * Create token instance from doctrine lexer token.
     *
     * @param array|null $token Token from abstract doctrine lexer.
     *
     * @return Token
     */
    public static function fromLexerToken($token)
    {
        if ($token === null) {
            return self::nullToken();
        }

        return new Token($token['type'], $token['value']);
    }

    /**
     * Named constructor for token with 'null' type.
     *
     * @return Token
     */
    public static function nullToken()
    {
        return new Token(self::TYPE_NULL);
    }

    /**
     * Named constructor for token with 'group' type.
     *
     * @param array $tokens   Array of tokens instances.
     * @param Token $operator Group operator.
     *
     * @return Token
     */
    public static function groupToken(array $tokens, Token $operator)
    {
        $token = new Token(self::TYPE_GROUP, $tokens);
        $token->setGroupOperator($operator);

        return $token;
    }

    /**
     * Named constructor for token with 'negative group' type.
     *
     * @param Token $token Some token.
     *
     * @return Token
     */
    public static function negativeGroupToken(Token $token)
    {
        if ($token->isNegativeGroup()) {
            /** @var Token $innerToken */
            $innerToken = $token->getValue();
            return new Token(
                $innerToken->getType(),
                $innerToken->getValue()
            );
        }

        return new Token(self::TYPE_NEGATIVE_GROUP, $token);
    }

    /**
     * @return boolean True if current token has 'word' type.
     */
    public function isWord()
    {
        return $this->type === self::TYPE_WORD;
    }

    /**
     * @return boolean True if current token has 'and' type.
     */
    public function isAnd()
    {
        return $this->type === self::TYPE_AND;
    }

    /**
     * @return boolean True if current token has 'or' type.
     */
    public function isOr()
    {
        return $this->type === self::TYPE_OR;
    }

    /**
     * @return boolean True if current token has 'not' type.
     */
    public function isNot()
    {
        return $this->type === self::TYPE_NOT;
    }

    /**
     * @return boolean True if current token has 'and', 'or' ot 'not' type.
     */
    public function isOperator()
    {
        return $this->isAnd() || $this->isOr() || $this->isNot();
    }

    /**
     * @return boolean True if current token has 'and' or 'or' type.
     */
    public function isBinaryOperator()
    {
        return $this->isAnd() || $this->isOr();
    }

    /**
     * @return boolean True if current token has 'open bracket' type.
     */
    public function isOpenBracket()
    {
        return $this->type === self::TYPE_OPEN_BRACKET;
    }

    /**
     * @return boolean True if current token has 'close bracket' type.
     */
    public function isCloseBracket()
    {
        return $this->type === self::TYPE_CLOSE_BRACKET;
    }

    /**
     * @return boolean True if current token has 'group' type.
     */
    public function isGroup()
    {
        return $this->type === self::TYPE_GROUP;
    }

    /**
     * @return boolean True if current token has 'group' type.
     */
    public function isNegativeGroup()
    {
        return $this->type === self::TYPE_NEGATIVE_GROUP;
    }

    /**
     * @return boolean True if current token has 'null' type.
     */
    public function isNull()
    {
        return $this->type === self::TYPE_NULL;
    }

    /**
     * @return Token[]|null|string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get priority of current token.
     *
     * @return integer
     */
    public function getPriority()
    {
        return self::$operationPriorities[$this->type];
    }

    /**
     * Set operator used in group.
     *
     * @param Token $operator A operator token.
     *
     * @return Token
     */
    public function setGroupOperator(Token $operator)
    {
        $this->groupOperator = $operator;

        return $this;
    }

    /**
     * Get operator used in group.
     *
     * @return Token
     */
    public function getGroupOperator()
    {
        return $this->groupOperator;
    }

    /**
     * Checks that current token has same type as specified token.
     *
     * @param Token $token A Token instance.
     *
     * @return boolean
     */
    public function isSameType(Token $token)
    {
        return $this->type === $token->type;
    }

    /**
     * @param boolean $normalized Flag, if set this token already normalized.
     *
     * @return Token
     */
    public function setNormalized($normalized)
    {
        $this->normalized = $normalized;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isNormalized()
    {
        return $this->normalized;
    }
}
