<?php

namespace CacheBundle\Document\Extractor;

use AppBundle\Enum\UnhandledEnumException;
use UserBundle\Enum\ThemeOptionExtractEnum;

/**
 * Class BasicDocumentContentExtractor
 *
 * @package CacheBundle\Document\Extractor
 */
class BasicDocumentContentExtractor implements DocumentContentExtractorInterface
{

    /**
     * Words and symbols which should be ignored.
     *
     * @var string[]
     */
    private static $unnecessaryWords = [
        'AND',
        'OR',
        'NOT',
        '(',
        ')',
        '+',
        '*',
        '-',
        '\\',
        '//',
    ];

    /**
     * Regexp for unnecessary parts of query.
     *
     * @var string[]
     */
    private static $unnecessaryRegexp = [
        '/\^\d+?/',
        '/~\d+?/',
    ];

    /**
     * @var integer
     */
    private $startExtractLen;

    /**
     * @var integer
     */
    private $contextExtractLen;

    /**
     * @var \Closure[]
     */
    private $converters = [];

    /**
     * @var array[]
     */
    private $keywordsCache = [];

    /**
     * BasicDocumentContentExtractor constructor.
     *
     * @param integer $startExtractLen   How many symbols extract for 'start'
     *                                   extract type.
     * @param integer $contextExtractLen How many symbols extract before and after
     *                                   keyword. Used for 'context' extract type.
     */
    public function __construct($startExtractLen, $contextExtractLen)
    {
        $this->startExtractLen = $startExtractLen;
        $this->contextExtractLen = $contextExtractLen;
    }

    /**
     * @param string                 $content   The document contents.
     * @param string                 $query     Search query.
     * @param ThemeOptionExtractEnum $extract   Extract type.
     * @param boolean                $highlight Should highlight matched keywords
     *                                          or not.
     *
     * @return ExtractionResult
     */
    public function extract(
        $content,
        $query,
        ThemeOptionExtractEnum $extract,
        $highlight = false
    ) {
        switch ($extract->getValue()) {
            //
            // We should not extract document content.
            //
            case ThemeOptionExtractEnum::NO:
                $text = '';
                $offset = '';
                $extractedLength = '';
                break;

            //
            // Extract specific numbers of characters from start of document.
            //
            case ThemeOptionExtractEnum::START:
                $text = mb_substr($content, 0, $this->startExtractLen);
                $offset = 0;
                $extractedLength = $this->startExtractLen;
                break;

            //
            // Extract specified number of character before and after first
            // matched keyword.
            //
            case ThemeOptionExtractEnum::CONTEXT:
                $keywords = $this->splitQueryOnKeywords($query);
                list ($offset, $keywordLength) = $this->getNearestKeyword($keywords, $content);

                if ($offset === -1) {
                    //
                    // We don't find any of search keywords. It maybe when matched
                    // keyword is found in another document property, like 'title'.
                    //
                    // In this case we fallback to 'start' extractor.
                    //
                    $text = mb_substr($content, 0, $this->startExtractLen);
                    $offset = 0;
                    $extractedLength = $this->startExtractLen;
                } else {
                    //
                    // Convert current offset into proper UTF value and extract
                    // text.
                    //
                    $converter = $this->createOffsetConverter($content);
                    $offset = $converter($offset);

                    //
                    // Compute start index and length of extract.
                    //
                    $extractStart = $offset - $this->contextExtractLen;

                    $overplus = 0;
                    if ($extractStart < 0) {
                        $overplus = abs($extractStart);
                        $extractStart = 0;
                    }

                    $extractedLength = $keywordLength + ($this->contextExtractLen * 2) - $overplus;

                    $text = mb_substr($content, $extractStart, $extractedLength, 'UTF-8');
                }
                break;

            default:
                throw UnhandledEnumException::fromInstance($extract);
        }

        if ($highlight) {
            // termporarily disable highlighting because of how it appears in emails
            // ~me 20200425
            // $text = $this->highlight($text, $query);
        }

        return new ExtractionResult($text, $offset, $extractedLength);
    }

    /**
     * @param string $query Search query.
     *
     * @return array
     */
    private function splitQueryOnKeywords($query)
    {
        //
        // Cache all splitted keywords in order to speedup processing.
        //
        if (! isset($this->keywordsCache[$query])) {
            $query = str_replace(self::$unnecessaryWords, '', $query);
            $query = preg_replace(self::$unnecessaryRegexp, '', $query);

            $this->keywordsCache[$query] = array_filter(\nspl\a\map('trim', mb_split(' ', $query)));
        }

        return $this->keywordsCache[$query];
    }

    /**
     * @param array  $keywords Array of keywords.
     * @param string $content  A ArticleDocument content.
     *
     * @return array
     */
    private function getNearestKeyword(array $keywords, $content)
    {
        $offset = -1;
        $keywordLength = 0;
        foreach ($keywords as $keyword) {
            $matched = [];
            preg_match('/(' . $keyword . ')/i', $content, $matched, PREG_OFFSET_CAPTURE);
            if (isset($matched[0]) && (($offset === -1) || ($offset > $matched[0][1]))) {
                $offset = $matched[0][1];
                $keywordLength = mb_strlen($matched[0][0], 'UTF-8');
            }
        }

        return [ $offset, $keywordLength ];
    }

    /**
     * @param string $content Document content.
     *
     * @return \Closure
     */
    private function createOffsetConverter($content)
    {
        //
        // Save all created converters in order to speedup processing.
        //
        $hash = sha1($content);
        if (! isset($this->converters[$hash])) {
            $contentLength = mb_strlen($content);
            $utfMap = [];

            for ($offset = 0; $offset < $contentLength; $offset++) {
                //
                // Single unicode character in ANSI format may have one and more
                // 'characters' (character codes). So for proper offset computation
                // we should get current character, compute it length in ANSI format
                // and create proper map between ANSI offset and Unicode offset.
                //
                $char = mb_substr($content, $offset, 1);
                $nonUtfLength = strlen($char);

                for ($charOffset = 0; $charOffset < $nonUtfLength; $charOffset++) {
                    $utfMap[] = $offset;
                }
            }

            $this->converters[$hash] = static function ($offset) use ($utfMap) {
                return $utfMap[$offset];
            };
        }

        return $this->converters[$hash];
    }

    /**
     * @param string $text  Highlighted text.
     * @param string $query Query.
     *
     * @return string
     */
    private function highlight($text, $query)
    {
        $keywords = $this->splitQueryOnKeywords($query);

        foreach ($keywords as $keyword) {
            // this is a very dumb line, but somewhere there is an issue with highlighting
            // where when "in' is present in a string, it only highlights it. E.g. if a search
            // string is: "building in public" the result is that it only highlights the word
            // "in". 
            // [Need a real fix]
            if (!preg_match("/\bin\b/i", $keyword)) {
                $text = preg_replace('/('. $keyword .')/i', '<span class=\'cw-keyword--highlight\'>$1</span>', $text);
            }
        }

        return $text;
    }
}
