<?php

namespace Tests\Helper;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class HtmlAsserter
 *
 * @package Helper
 */
class HtmlAsserter
{

    /**
     * @var Crawler
     */
    private $root;

    /**
     * @var HtmlAsserter|null
     */
    private $parent;

    /**
     * HtmlAsserter constructor.
     *
     * @param Crawler      $root   A Crawler instance.
     * @param HtmlAsserter $parent A parent Crawler instance.
     */
    public function __construct(Crawler $root, HtmlAsserter $parent = null)
    {
        $this->root = $root;
        $this->parent = $parent;
    }

    /**
     * @param string  $selector Node selector.
     * @param integer $count    Expected node numbers. Check that at least one
     *                          exists if -1.
     *
     * @return HtmlAsserter
     */
    public function hasNode($selector, $count = 1)
    {
        $nodes = $this->root->filter($selector);

        if ($count === -1) {
            TestCase::assertGreaterThan(0, count($nodes), sprintf(
                'Expects at least one node \'%s\' but got zero',
                $selector
            ));
        } else {
            TestCase::assertCount($count, $nodes, sprintf(
                'Expects %d of \'%s\' nodes but got another count',
                $count,
                $selector
            ));
        }

        return $this;
    }

    /**
     * @param string $selector Node selector.
     *
     * @return HtmlAsserter
     */
    public function hasNotNode($selector)
    {
        TestCase::assertCount(0, $this->root->filter($selector), sprintf(
            'Node \'%s\' is exists but should\'nt',
            $selector
        ));

        return $this;
    }

    /**
     * @param string $class Expected node class.
     *
     * @return HtmlAsserter
     */
    public function hasClass($class)
    {
        TestCase::assertContains($class, $this->root->attr('class'), sprintf(
            'Current node should has \'%s\' class but it has not',
            $class
        ));

        return $this;
    }

    /**
     * @param string $name  HTML node attribute name.
     * @param string $value Expected value.
     *
     * @return HtmlAsserter
     */
    public function hasAttr($name, $value = null)
    {
        $nodeValue = $this->root->attr($name);

        if ($value === null) {
            TestCase::assertNotNull($nodeValue, sprintf(
                'Node attribute \'%s\' should exists but it has not',
                $name
            ));
        } else {
            TestCase::assertEquals($value, $nodeValue, sprintf(
                'Node attribute \'%s\' should match to \'%s\' but it has \'%s\'',
                $name,
                $value,
                $nodeValue
            ));
        }

        return $this;
    }

    /**
     * @param string  $content Expected text content.
     * @param boolean $strict  Should content only specified values or not.
     *
     * @return HtmlAsserter
     */
    public function contains($content, $strict = false)
    {
        $nodeContent = $this->root->getNode(0)->textContent;

        if ($strict) {
            TestCase::assertEquals($content, $nodeContent, sprintf(
                'Node should contains only \'%s\' but it has not',
                $content
            ));
        } else {
            TestCase::assertContains($content, $nodeContent, sprintf(
                'Node should contains \'%s\' but it has not',
                $content
            ));
        }

        return $this;
    }

    /**
     * @param string  $content Unexpected text content.
     * @param boolean $strict  Should content only specified values or not.
     *
     * @return $this
     */
    public function notContains($content, $strict = false)
    {
        $nodeContent = $this->root->getNode(0)->textContent;

        if ($strict) {
            TestCase::assertNotEquals($content, $nodeContent, sprintf(
                'Node should\'nt contains \'%s\' but it has',
                $content
            ));
        } else {
            TestCase::assertNotContains($content, $nodeContent, sprintf(
                'Node should\'nt contains \'%s\' but it has',
                $content
            ));
        }

        return $this;
    }

    /**
     * @param string $regex Regular expression.
     *
     * @return HtmlAsserter
     */
    public function regexContent($regex)
    {
        TestCase::assertRegExp($regex, $this->root->getNode(0)->textContent, sprintf(
            'Content of node should match to \'%s\' but it has not',
            $regex
        ));

        return $this;
    }

    /**
     * @param string $selector Node selector.
     *
     * @return HtmlAsserter
     */
    public function with($selector)
    {
        $this->hasNode($selector, -1);

        return new static($this->root->filter($selector), $this);
    }

    /**
     * @param integer $idx Child node index.
     *
     * @return HtmlAsserter
     */
    public function child($idx)
    {
        return new static(new Crawler($this->root->getNode($idx)), $this);
    }

    /**
     * @return HtmlAsserter
     */
    public function end()
    {
        return $this->parent !== null ? $this->parent : $this;
    }

    /**
     * @return HtmlAsserter
     */
    public function dump()
    {
        echo $this->root->html();

        return $this;
    }
}
