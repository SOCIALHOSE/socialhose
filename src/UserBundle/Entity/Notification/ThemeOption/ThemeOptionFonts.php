<?php

namespace UserBundle\Entity\Notification\ThemeOption;

/**
 * Class ThemeOptionFonts
 * @package UserBundle\Entity\Notification\ThemeOption
 */
class ThemeOptionFonts implements \Serializable
{

    /**
     * @var ThemeOptionFont
     */
    private $header;

    /**
     * @var ThemeOptionFont
     */
    private $tableOfContents;

    /**
     * @var ThemeOptionFont
     */
    private $feedTitle;

    /**
     * @var ThemeOptionFont
     */
    private $articleHeadline;

    /**
     * @var ThemeOptionFont
     */
    private $source;

    /**
     * @var ThemeOptionFont
     */
    private $author;

    /**
     * @var ThemeOptionFont
     */
    private $date;

    /**
     * @var ThemeOptionFont
     */
    private $articleContent;

    /**
     * ThemeOptionFonts constructor.
     *
     * @param ThemeOptionFont $header          A ThemeOptionFont instance.
     * @param ThemeOptionFont $tableOfContents A ThemeOptionFont instance.
     * @param ThemeOptionFont $feedTitle       A ThemeOptionFont instance.
     * @param ThemeOptionFont $articleHeadline A ThemeOptionFont instance.
     * @param ThemeOptionFont $source          A ThemeOptionFont instance.
     * @param ThemeOptionFont $author          A ThemeOptionFont instance.
     * @param ThemeOptionFont $date            A ThemeOptionFont instance.
     * @param ThemeOptionFont $articleContent  A ThemeOptionFont instance.
     */
    public function __construct(
        ThemeOptionFont $header,
        ThemeOptionFont $tableOfContents,
        ThemeOptionFont $feedTitle,
        ThemeOptionFont $articleHeadline,
        ThemeOptionFont $source,
        ThemeOptionFont $author,
        ThemeOptionFont $date,
        ThemeOptionFont $articleContent
    ) {
        $this->header = $header;
        $this->tableOfContents = $tableOfContents;
        $this->feedTitle = $feedTitle;
        $this->articleHeadline = $articleHeadline;
        $this->source = $source;
        $this->author = $author;
        $this->date = $date;
        $this->articleContent = $articleContent;
    }

    /**
     * @return ThemeOptionFont
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param ThemeOptionFont $header A ThemeOptionFont instance.
     *
     * @return ThemeOptionFonts
     */
    public function setHeader(ThemeOptionFont $header)
    {
        $this->header = $header;

        return $this;
    }

    /**
     * @return ThemeOptionFont
     */
    public function getTableOfContents()
    {
        return $this->tableOfContents;
    }

    /**
     * @param ThemeOptionFont $tableOfContents A ThemeOptionFont instance.
     *
     * @return ThemeOptionFonts
     */
    public function setTableOfContents(ThemeOptionFont $tableOfContents)
    {
        $this->tableOfContents = $tableOfContents;

        return $this;
    }

    /**
     * @return ThemeOptionFont
     */
    public function getFeedTitle()
    {
        return $this->feedTitle;
    }

    /**
     * @param ThemeOptionFont $feedTitle A ThemeOptionFont instance.
     *
     * @return ThemeOptionFonts
     */
    public function setFeedTitle(ThemeOptionFont $feedTitle)
    {
        $this->feedTitle = $feedTitle;

        return $this;
    }

    /**
     * @return ThemeOptionFont
     */
    public function getArticleHeadline()
    {
        return $this->articleHeadline;
    }

    /**
     * @param ThemeOptionFont $articleHeadline A ThemeOptionFont instance.
     *
     * @return ThemeOptionFonts
     */
    public function setArticleHeadline(ThemeOptionFont $articleHeadline)
    {
        $this->articleHeadline = $articleHeadline;

        return $this;
    }

    /**
     * @return ThemeOptionFont A ThemeOptionFont instance.
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param ThemeOptionFont $source A ThemeOptionFont instance.
     *
     * @return ThemeOptionFonts
     */
    public function setSource(ThemeOptionFont $source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return ThemeOptionFont
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param ThemeOptionFont $author A ThemeOptionFont instance.
     *
     * @return ThemeOptionFonts
     */
    public function setAuthor(ThemeOptionFont $author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return ThemeOptionFont
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param ThemeOptionFont $date A ThemeOptionFont instance.
     *
     * @return ThemeOptionFonts
     */
    public function setDate(ThemeOptionFont $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return ThemeOptionFont
     */
    public function getArticleContent()
    {
        return $this->articleContent;
    }

    /**
     * @param ThemeOptionFont $articleContent A ThemeOptionFont instance.
     *
     * @return ThemeOptionFonts
     */
    public function setArticleContent(ThemeOptionFont $articleContent)
    {
        $this->articleContent = $articleContent;

        return $this;
    }

    /**
     * String representation of object.
     *
     * @return string the string representation of the object or null.
     */
    public function serialize()
    {
        return serialize([
            $this->header,
            $this->tableOfContents,
            $this->feedTitle,
            $this->articleHeadline,
            $this->source,
            $this->author,
            $this->date,
            $this->articleContent,
        ]);
    }

    /**
     * Constructs the object
     *
     * @param string $serialized The string representation of the object.
     *
     * @return void
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        $this->header = $data[0];
        $this->tableOfContents = $data[1];
        $this->feedTitle = $data[2];
        $this->articleHeadline = $data[3];
        $this->source = $data[4];
        $this->author = $data[5];
        $this->date = $data[6];
        $this->articleContent = $data[7];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'header' => $this->header->toArray(),
            'tableOfContents' => $this->tableOfContents->toArray(),
            'feedTitle' => $this->feedTitle->toArray(),
            'articleHeadline' => $this->articleHeadline->toArray(),
            'source' => $this->source->toArray(),
            'author' => $this->author->toArray(),
            'date' => $this->date->toArray(),
            'articleContent' => $this->articleContent->toArray(),
        ];
    }
}
