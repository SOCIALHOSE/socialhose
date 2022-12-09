<?php

namespace Common\Util\Index;

use IndexBundle\Index\Source\SourceIndexInterface;
use IndexBundle\Model\Generator\SourceDocumentGenerator;
use IndexBundle\Model\DocumentInterface;
use IndexBundle\Util\Initializer\SourceIndexInitializer;

/**
 * Class InternalSourceConnection
 * @package Common\Util\Index
 */
class InternalSourceConnection extends AbstractTestIndexConnection implements SourceIndexInterface
{

    /**
     * @var SourceDocumentGenerator
     */
    private $documentGenerator;

    /**
     * ExternalIndexConnection constructor.
     *
     * @param SourceIndexInterface $index A SourceIndexInterface interface.
     */
    public function __construct(SourceIndexInterface $index)
    {
        parent::__construct($index);
        $this->documentGenerator = new SourceDocumentGenerator();
    }

    /**
     * Setup internal index.
     *
     * @return void
     */
    public function setup()
    {
        SourceIndexInitializer::initialize($this);
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
