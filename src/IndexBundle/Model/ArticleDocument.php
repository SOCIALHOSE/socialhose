<?php

namespace IndexBundle\Model;

use CacheBundle\Entity\Document;
use IndexBundle\Index\Internal\InternalIndexInterface;
use IndexBundle\Index\Strategy\HoseIndexStrategy;

/**
 * Class ArticleDocument
 *
 * @package IndexBundle\Model
 */
class ArticleDocument extends AbstractDocument implements ArticleDocumentInterface
{

    /**
     * Map between strategy class and platform.
     *
     * @var array
     */
    private static $strategyToTypeMap = [
        HoseIndexStrategy::class => self::PLATFORM_hose,
        InternalIndexInterface::class => self::PLATFORM_hose,
    ];

    /**
     * Get document id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->data['sequence'];
    }

    /**
     * Get platform from which we get this document.
     *
     * @return string
     */
    public function getPlatform()
    {
        return self::$strategyToTypeMap[get_class($this->strategy)];
    }

    /**
     * Create proper source document instance from this article document.
     *
     * @return array
     */
    public function toSourceDocumentData()
    {
        $data = $this->getNormalizedData();

        return [
            'id' => $data['source']['id'],
            'title' => $data['source']['title'],
            'url' => $data['source']['link'],
            'country' => $data['source']['country'],
            'state' => $data['source']['state'],
            'city' => $data['source']['city'],
            'section' => $data['source']['section'],
            'lang' => $data['language'],
            'deleted' => 0,
            'type' => $data['source']['type'],
            'listIds' => [],
        ];
    }

    /**
     * @return Document
     */
    public function toDocumentEntity()
    {
        return Document::create()
            ->setData($this->data)
            ->setPlatform(ArticleDocumentInterface::PLATFORM_hose)
            ->setId($this->getId());
    }

    /**
     * Is triggered when invoking inaccessible methods in an object context.
     *
     * @param string $name      Method name.
     * @param array  $arguments Methid arguments.
     *
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __call($name, array $arguments)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        return null;
    }
}
