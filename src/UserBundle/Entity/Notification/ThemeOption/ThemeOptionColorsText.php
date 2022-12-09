<?php

namespace UserBundle\Entity\Notification\ThemeOption;

/**
 * Class ThemeOptionColorsText
 * @package UserBundle\Entity\Notification\ThemeOption
 */
class ThemeOptionColorsText implements \Serializable
{

    const DEFAULT_HEADER = 'rgba(255, 255, 255, 1)';
    const DEFAULT_ARTICLE_HEADLINE = 'rgba(0, 147, 176, 1)';
    const DEFAULT_ARTICLE_CONTENT = 'rgba(102, 102, 102, 1)';
    const DEFAULT_AUTHOR = 'rgba(143, 43, 140, 1)';
    const DEFAULT_PUBLISH_DATE = 'rgba(109, 110, 113, 1)';
    const DEFAULT_SOURCE = 'rgba(82, 83, 85, 1)';

    /**
     * CSS RGBA.
     *
     * ```
     * rgba(125, 125, 125, 0.5);
     * ```
     *
     * @var string
     */
    private $header;

    /**
     * CSS RGBA.
     *
     * ```
     * rgba(125, 125, 125, 0.5);
     * ```
     *
     * @var string
     */
    private $articleHeadline;

    /**
     * CSS RGBA.
     *
     * ```
     * rgba(125, 125, 125, 0.5);
     * ```
     *
     * @var string
     */
    private $articleContent;

    /**
     * CSS RGBA.
     *
     * ```
     * rgba(125, 125, 125, 0.5);
     * ```
     *
     * @var string
     */
    private $author;

    /**
     * CSS RGBA.
     *
     * ```
     * rgba(125, 125, 125, 0.5);
     * ```
     *
     * @var string
     */
    private $publishDate;

    /**
     * CSS RGBA.
     *
     * ```
     * rgba(125, 125, 125, 0.5);
     * ```
     *
     * @var string
     */
    private $source;

    /**
     * ThemeOptionColorsText constructor.
     *
     * @param string $header          Header text color.
     * @param string $articleHeadline Article headline text color.
     * @param string $articleContent  Article content text color.
     * @param string $author          Author text color.
     * @param string $publishDate     Publish date text color.
     * @param string $source          Source text color.
     */
    public function __construct(
        $header = self::DEFAULT_HEADER,
        $articleHeadline = self::DEFAULT_ARTICLE_HEADLINE,
        $articleContent = self::DEFAULT_ARTICLE_CONTENT,
        $author = self::DEFAULT_AUTHOR,
        $publishDate = self::DEFAULT_PUBLISH_DATE,
        $source = self::DEFAULT_SOURCE
    ) {
        $this->header = trim($header);
        $this->articleHeadline = trim($articleHeadline);
        $this->articleContent = trim($articleContent);
        $this->author = trim($author);
        $this->publishDate = trim($publishDate);
        $this->source = trim($source);
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param string $header Header text color.
     *
     * @return ThemeOptionColorsText
     */
    public function setHeader($header)
    {
        $this->header = $header;

        return $this;
    }

    /**
     * @return string
     */
    public function getArticleHeadline()
    {
        return $this->articleHeadline;
    }

    /**
     * @param string $articleHeadline Article headline text color.
     *
     * @return ThemeOptionColorsText
     */
    public function setArticleHeadline($articleHeadline)
    {
        $this->articleHeadline = $articleHeadline;

        return $this;
    }

    /**
     * @return string
     */
    public function getArticleContent()
    {
        return $this->articleContent;
    }

    /**
     * @param string $articleContent Article content text color.
     *
     * @return ThemeOptionColorsText
     */
    public function setArticleContent($articleContent)
    {
        $this->articleContent = $articleContent;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param string $author Author text color.
     *
     * @return ThemeOptionColorsText
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return string
     */
    public function getPublishDate()
    {
        return $this->publishDate;
    }

    /**
     * @param string $publishDate Publish date text color.
     *
     * @return ThemeOptionColorsText
     */
    public function setPublishDate($publishDate)
    {
        $this->publishDate = $publishDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $source Source text color.
     *
     * @return ThemeOptionColorsText
     */
    public function setSource($source)
    {
        $this->source = $source;

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
            $this->articleHeadline,
            $this->articleContent,
            $this->author,
            $this->publishDate,
            $this->source,
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
        $this->articleHeadline = $data[1];
        $this->articleContent = $data[2];
        $this->author = $data[3];
        $this->publishDate = $data[4];
        $this->source = $data[5];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'header' => $this->header,
            'articleHeadline' => $this->articleHeadline,
            'articleContent' => $this->articleContent,
            'author' => $this->author,
            'publishDate' => $this->publishDate,
            'source' => $this->source,
        ];
    }
}
