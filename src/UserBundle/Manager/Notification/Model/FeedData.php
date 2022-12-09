<?php

namespace UserBundle\Manager\Notification\Model;

use CacheBundle\Entity\Document;
use Doctrine\Common\Proxy\Exception\UnexpectedValueException;
use IndexBundle\Model\ArticleDocumentInterface;

/**
 * Class FeedData
 *
 * Holds all necessary data for render feed in notifications.
 *
 * @package UserBundle\Manager\Notification\Model
 */
class FeedData implements \Countable, \IteratorAggregate
{

    /**
     * Feed name.
     *
     * @var string
     */
    private $name;

    /**
     * Array of fetched documents.
     *
     * @var Document[]
     */
    private $documents;

    /**
     * Count of documents.
     *
     * @var integer
     */
    private $documentsCount;

    /**
     * FeedData constructor.
     *
     * @param string                     $name      Feed name.
     * @param ArticleDocumentInterface[] $documents Array of documents.
     */
    public function __construct($name, array $documents)
    {
        $this->name = $name;

        if (! \nspl\a\all($documents, \nspl\f\rpartial(\app\op\isInstanceOf, ArticleDocumentInterface::class))) {
            throw new UnexpectedValueException(sprintf(
                'All documents should be instances of %s',
                ArticleDocumentInterface::class
            ));
        }

        $this->documents = \nspl\a\map(\nspl\op\methodCaller('normalize'), $documents);
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Fet documents.
     *
     * @return ArticleDocumentInterface[]
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * Get documents count.
     *
     * @return integer
     */
    public function getDocumentsCount()
    {
        if ($this->documentsCount === null) {
            $this->documentsCount = count($this->documents);
        }

        return $this->documentsCount;
    }

    /**
     * Count elements of an object
     *
     * @return integer The custom count as an integer.
     *
     * The return value is cast to an integer.
     */
    public function count()
    {
        return $this->getDocumentsCount();
    }

    /**
     * Retrieve an external iterator.
     *
     * @return \Traversable An instance of an object implementing Iterator or Traversable.
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->documents);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'name' => $this->name,
            'documents' => $this->documents,
            'documentsCount' => $this->documentsCount,
        ];
    }
}
