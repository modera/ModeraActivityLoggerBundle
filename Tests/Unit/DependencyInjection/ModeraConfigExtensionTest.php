<?php

namespace Modera\ConfigBundle\Tests\Unit\DependencyInjection;

use Modera\ConfigBundle\DependencyInjection\ModeraConfigExtension;
use Modera\ConfigBundle\Twig\TwigExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class ModeraConfigExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $ext = new ModeraConfigExtension();

        $builder = new ContainerBuilder();

        $ext->load(array(), $builder);

        $def = $builder->getDefinition('modera_config.twig.twig_extension');

        $this->assertEquals(TwigExtension::clazz(), $def->getClass());
        $this->assertEquals(1, count($def->getTag('twig.extension')));

        /* @var Reference $arg */
        $arg = $def->getArgument(0);
        $this->assertNotNull($arg);
        $this->assertEquals('modera_config.configuration_entries_manager', (string) $arg);
    }
}
