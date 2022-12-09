<?php

namespace Common\Util\Index;

use IndexBundle\Index\External\ExternalIndexInterface;
use IndexBundle\Model\Generator\ExternalDocumentGenerator;
use IndexBundle\Model\DocumentInterface;
use IndexBundle\Util\Initializer\ExternalIndexInitializer;

/**
 * Class ExternalIndexConnection
 *
 * @package Common\Util\Index
 */
class ExternalIndexConnection extends AbstractTestIndexConnection
{

    /**
     * @var ExternalDocumentGenerator
     */
    private $documentGenerator;

    /**
     * ExternalIndexConnection constructor.
     *
     * @param ExternalIndexInterface $index A ExternalIndexInterface interface.
     */
    public function __construct(ExternalIndexInterface $index)
    {
        parent::__construct($index);
        $this->documentGenerator = new ExternalDocumentGenerator();
    }

    /**
     * Setup external index.
     *
     * @return void
     */
    public function setup()
    {
        ExternalIndexInitializer::initialize($this);
    }

    /**
     * Create new document for this index.
     *
     * @return DocumentInterface
     */
    public function createDocument()
    {
        return $this->documentGenerator->generate();
    }
}
