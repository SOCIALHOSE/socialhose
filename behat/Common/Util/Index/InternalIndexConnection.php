<?php

namespace Common\Util\Index;

use IndexBundle\Index\Internal\InternalIndexInterface;
use IndexBundle\Model\Generator\InternalDocumentGenerator;
use IndexBundle\Model\DocumentInterface;
use IndexBundle\Util\Initializer\InternalIndexInitializer;

/**
 * Class InternalIndexConnection
 * @package Common\Util\Index
 */
class InternalIndexConnection extends AbstractTestIndexConnection
{

    /**
     * @var InternalDocumentGenerator
     */
    private $documentGenerator;

    /**
     * ExternalIndexConnection constructor.
     *
     * @param InternalIndexInterface $index A InternalIndexInterface interface.
     */
    public function __construct(InternalIndexInterface $index)
    {
        parent::__construct($index);
        $this->documentGenerator = new InternalDocumentGenerator();
    }

    /**
     * Setup internal index.
     *
     * @return void
     */
    public function setup()
    {
        InternalIndexInitializer::initialize($this);
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
