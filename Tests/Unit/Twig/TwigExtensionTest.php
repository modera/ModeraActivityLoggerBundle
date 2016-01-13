<?php

namespace Modera\ConfigBundle\Tests\Unit\Twig;

use Modera\ConfigBundle\Twig\TwigExtension;
use SensioLabs\Security\Exception\RuntimeException;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class TwigExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TwigExtension
     */
    private $ext;

    private $configEntriesManager;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->configEntriesManager = \Phake::mock('Modera\ConfigBundle\Config\ConfigurationEntriesManagerInterface');

        $this->ext = new TwigExtension($this->configEntriesManager);
    }

    public function testGetFunctions()
    {
        /* @var \Twig_SimpleFunction[] $functions*/
        $functions = $this->ext->getFunctions();

        $this->assertEquals(1, count($functions));
        $this->assertInstanceOf('Twig_SimpleFunction', $functions[0]);

        $fn = $functions[0];

        $this->assertEquals('modera_config_value', $fn->getName());

        $callable = $fn->getCallable();
        $this->assertSame($this->ext, $callable[0]);
        $this->assertEquals('twigModeraConfigValue', $callable[1]);
    }

    public function testTwigModeraConfigValueNotStrict()
    {
        $value = $this->ext->twigModeraConfigValue('fooproperty', false);

        $this->assertNull($value);

        \Phake::verify($this->configEntriesManager)->findOneByName('fooproperty');

        // ---

        $property = \Phake::mock('Modera\ConfigBundle\Config\ConfigurationEntryInterface');
        \Phake::when($property)
            ->getValue()
            ->thenReturn('barvalue')
        ;

        \Phake::when($this->configEntriesManager)
            ->findOneByName('barproperty')
            ->thenReturn($property)
        ;

        $this->assertEquals('barvalue', $this->ext->twigModeraConfigValue('barproperty', false));
    }

    /**
     * @expectedException RuntimeException
     */
    public function testTwigModeraConfigValueStrict()
    {
        \Phake::when($this->configEntriesManager)
            ->findOneByNameOrDie('foo')
            ->thenThrow(new \RuntimeException('ololo'))
        ;

        $this->ext->twigModeraConfigValue('foo');
    }
}
