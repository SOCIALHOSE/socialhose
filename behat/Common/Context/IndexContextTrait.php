<?php

namespace Common\Context;

use Behat\Gherkin\Node\TableNode;
use Common\Util\Index\ExternalIndexConnection;
use Common\Util\Index\InternalIndexConnection;
use Common\Util\Index\InternalSourceConnection;
use Common\Util\Index\TestIndexConnectionInterface;

/**
 * Class IndexContextTrait
 * Contains steps definitions for working with indices.
 *
 * @package Common\Context
 */
trait IndexContextTrait
{

    /**
     * @var ExternalIndexConnection
     */
    protected $externalIndex;

    /**
     * @var InternalIndexConnection
     */
    protected $internalIndex;

    /**
     * @var InternalSourceConnection
     */
    protected $sourceIndex;

    /**
     * @Transform /^([Ee]xternal|[Ii]nternal|[Ss]ource)$/
     *
     * @param string $index Index name.
     *
     * @return TestIndexConnectionInterface
     */
    public function getConnectionByName($index)
    {
        $index = strtolower($index);

        switch ($index) {
            case 'external':
                return $this->externalIndex;

            case 'internal':
                return $this->internalIndex;

            case 'source':
                return $this->sourceIndex;
        }

        throw new \InvalidArgumentException("Unknown index '{$index}'");
    }

    /**
     * Json parameters should have only necessary fields. Over will be auto
     * generated.
     *
     * @Given /^(?:I add|[Hh]as) new document (?:in|to) (?P<connection>(external|internal|source)) index$/
     *
     * @param TestIndexConnectionInterface $index A TestIndexConnectionInterface
     *                                            instance.
     * @param TableNode                    $table A TableNode instance.
     *
     * @return void
     */
    public function indexDocument(
        TestIndexConnectionInterface $index,
        TableNode $table
    ) {
        /** @var AbstractContext $this */
        $document = $index->createDocument();

        $params = [];
        $tableData = $table->getTable();
        foreach ($tableData as $row) {
            $params[current($row)] = $this->processor->process(next($row));
        }

        foreach ($params as $name => $value) {
            $document[$name] = $value;
        }

        $index->index($document);
        // Wait to insure that all fixtures was indexed.
        usleep(100000);
    }

    /**
     * @Then /^(?P<connection>([Ee]xternal|[Ii]nternal|[Ss]ource)) index has (?P<count>\d+) document[s]?$/
     *
     * @param TestIndexConnectionInterface $connection A
     *                                                 TestIndexConnectionInterface
     *                                                 instance.
     * @param integer                      $count      A expected documents
     *                                                 count.
     * @param TableNode                    $table      A TableNode instance.
     *
     * @return void
     */
    public function hasDocuments(
        TestIndexConnectionInterface $connection,
        $count,
        TableNode $table
    ) {
        /** @var AbstractContext $this */
        $tableData = $table->getTable();
        $factory = $connection->getFilterFactory();
        $filters = [];
        foreach ($tableData as $row) {
            $field = current($row);
            $type = next($row);
            $value = next($row);

            if ($type === 'in') {
                $value = array_filter(array_map('trim', explode(',', $value)));
            }

            $filters[] =
                $factory->{$type}($field, $this->processor->process($value));
        }

        $results = $connection->createRequestBuilder()
            ->setFilters($filters)
            ->build()
            ->execute();

        self::assertCount((int) $count, $results);
    }
}
