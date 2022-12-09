<?php

namespace UserBundle\Entity\Notification\ThemeOption;

use UserBundle\Enum\ThemeOptionsTableOfContentsEnum;
use UserBundle\Enum\ThemeOptionsUserCommentsEnum;

/**
 * Class ThemeOptionShowInfo
 * @package UserBundle\Entity\Notification\ThemeOption
 */
class ThemeOptionShowInfo implements \Serializable
{

    /**
     * @var boolean
     */
    private $sourceCountry;

    /**
     * @var boolean
     */
    private $articleSentiment;

    /**
     * @var boolean
     */
    private $articleCount;

    /**
     * @var boolean
     */
    private $images;

    /**
     * @var boolean
     */
    private $sharingOptions;

    /**
     * @var boolean
     */
    private $sectionDivider;

    /**
     * @var ThemeOptionsUserCommentsEnum
     */
    private $userComments;

    /**
     * @var ThemeOptionsTableOfContentsEnum
     */
    private $tableOfContents;

    /**
     * ThemeOptionShowInfo constructor.
     *
     * @param ThemeOptionsUserCommentsEnum|string    $userComments     A ThemeOptionsUserCommentsEnum
     *                                                                 instance.
     * @param ThemeOptionsTableOfContentsEnum|string $tableOfContents  A ThemeOptionsTableOfContentsEnum
     *                                                                 instance.
     * @param boolean                                $sourceCountry    Show or not source country.
     * @param boolean                                $articleSentiment Show or not article sentiment.
     * @param boolean                                $articleCount     Show or not article count.
     * @param boolean                                $images           Show or not article images.
     * @param boolean                                $sharingOptions   Show or not sharing options.
     * @param boolean                                $sectionDivider   Show or not section divider.
     */
    public function __construct(
        $userComments,
        $tableOfContents,
        $sourceCountry = false,
        $articleSentiment = true,
        $articleCount = true,
        $images = true,
        $sharingOptions = true,
        $sectionDivider = false
    ) {
        $this->sourceCountry = (bool) $sourceCountry;
        $this->articleSentiment = (bool) $articleSentiment;
        $this->articleCount = (bool) $articleCount;
        $this->images = (bool) $images;
        $this->sharingOptions = (bool) $sharingOptions;
        $this->sectionDivider = (bool) $sectionDivider;
        $this->setUserComments($userComments);
        $this->setTableOfContents($tableOfContents);
    }

    /**
     * @return boolean
     */
    public function isSourceCountry()
    {
        return $this->sourceCountry;
    }

    /**
     * @param boolean $sourceCountry Show source country.
     *
     * @return ThemeOptionShowInfo
     */
    public function setSourceCountry($sourceCountry = true)
    {
        $this->sourceCountry = $sourceCountry;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isArticleSentiment()
    {
        return $this->articleSentiment;
    }

    /**
     * @param boolean $articleSentiment Show article sentiment.
     *
     * @return ThemeOptionShowInfo
     */
    public function setArticleSentiment($articleSentiment = true)
    {
        $this->articleSentiment = $articleSentiment;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isArticleCount()
    {
        return $this->articleCount;
    }

    /**
     * @param boolean $articleCount Show article count.
     *
     * @return ThemeOptionShowInfo
     */
    public function setArticleCount($articleCount = true)
    {
        $this->articleCount = $articleCount;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isImages()
    {
        return $this->images;
    }

    /**
     * @param boolean $images Show or not articles images.
     *
     * @return ThemeOptionShowInfo
     */
    public function setImages($images = true)
    {
        $this->images = $images;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isSharingOptions()
    {
        return $this->sharingOptions;
    }

    /**
     * @param boolean $sharingOptions Show or not sharing options.
     *
     * @return ThemeOptionShowInfo
     */
    public function setSharingOptions($sharingOptions = true)
    {
        $this->sharingOptions = $sharingOptions;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isSectionDivider()
    {
        return $this->sectionDivider;
    }

    /**
     * @param boolean $sectionDivider Show section divider or not.
     *
     * @return ThemeOptionShowInfo
     */
    public function setSectionDivider($sectionDivider = true)
    {
        $this->sectionDivider = $sectionDivider;

        return $this;
    }

    /**
     * @return ThemeOptionsUserCommentsEnum
     */
    public function getUserComments()
    {
        return $this->userComments;
    }

    /**
     * @param ThemeOptionsUserCommentsEnum|string $userComments A ThemeOptionsUserCommentsEnum
     *                                                          instance.
     *
     * @return ThemeOptionShowInfo
     */
    public function setUserComments($userComments)
    {
        if (is_string($userComments)) {
            $userComments = new ThemeOptionsUserCommentsEnum($userComments);
        }
        $this->userComments = $userComments;

        return $this;
    }

    /**
     * @return ThemeOptionsTableOfContentsEnum
     */
    public function getTableOfContents()
    {
        return $this->tableOfContents;
    }

    /**
     * @param ThemeOptionsTableOfContentsEnum|string $tableOfContents A ThemeOptionsTableOfContentsEnum
     *                                                                instance.
     *
     * @return ThemeOptionShowInfo
     */
    public function setTableOfContents($tableOfContents)
    {
        if (is_string($tableOfContents)) {
            $tableOfContents = new ThemeOptionsTableOfContentsEnum($tableOfContents);
        }
        $this->tableOfContents = $tableOfContents;

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
            $this->sourceCountry,
            $this->articleSentiment,
            $this->articleCount,
            $this->images,
            $this->sharingOptions,
            $this->sectionDivider,
            $this->userComments,
            $this->tableOfContents,
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

        $this->sourceCountry = $data[0];
        $this->articleSentiment = $data[1];
        $this->articleCount = $data[2];
        $this->images = $data[3];
        $this->sharingOptions = $data[4];
        $this->sectionDivider = $data[5];
        $this->userComments = $data[6];
        $this->tableOfContents = $data[7];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'sourceCountry' => $this->sourceCountry,
            'articleSentiment' => $this->articleSentiment,
            'articleCount' => $this->articleCount,
            'images' => $this->images,
            'sharingOptions' => $this->sharingOptions,
            'sectionDivider' => $this->sectionDivider,
            'userComments' => $this->userComments->getValue(),
            'tableOfContents' => $this->tableOfContents->getValue(),
        ];
    }
}
