<?php

namespace Modera\RoutingBundle\Tests\Unit;

use Modera\RoutingBundle\ModeraRoutingBundle;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2015 Modera Foundation
 */
class ModeraRoutingBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $containerBuilder = \Phake::mock('Symfony\Component\DependencyInjection\ContainerBuilder');

        $bundle = new ModeraRoutingBundle();

        $bundle->build($containerBuilder);

        \Phake::verify($containerBuilder, \Phake::atLeast(2))
            ->addCompilerPass($this->isInstanceOf('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface')
        );
    }
}
