<?php

namespace Modera\FoundationBundle\Tests\Unit\Twig;

use Modera\FoundationBundle\Twig\Extension;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class ExtensionTest extends \PHPUnit_Framework_TestCase
{
    /* @var Extension $ext */
    private $ext;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->ext = new Extension();
    }

    public function testFilter_prepend_every_line()
    {
        $input = <<<TEXT
foo
bar
TEXT;

        $expectedOutput = <<<TEXT
   foo
   bar
TEXT;

        $this->assertEquals($expectedOutput, $this->ext->filter_prepend_every_line($input, 3));

        // ---

        $input = <<<TEXT
 foo
  bar
TEXT;

        $expectedOutput = <<<TEXT
---- foo
----  bar
TEXT;
        $this->assertEquals($expectedOutput, $this->ext->filter_prepend_every_line($input, 4, '-'));

        // --

        $input = <<<JSON
{
    foo: {}
}
JSON;

        $expectedOutput = <<<JSON
{
        foo: {}
    }
JSON;

        $this->assertEquals($expectedOutput, $this->ext->filter_prepend_every_line($input, 4, ' ', true));


    }
}