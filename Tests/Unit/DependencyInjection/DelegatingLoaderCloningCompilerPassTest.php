<?php

namespace Modera\RoutingBundle\Tests\Unit\DependencyInjection;

use Modera\RoutingBundle\DependencyInjection\DelegatingLoaderCloningCompilerPass;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2015 Modera Foundation
 */
class DelegatingLoaderCloningCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $routingLoaderWannaBe = \Phake::mock('Symfony\Component\DependencyInjection\Definition');
        $containerBuilder = \Phake::mock('Symfony\Component\DependencyInjection\ContainerBuilder');

        \Phake::when($containerBuilder)->getDefinition('routing.loader')->thenReturn($routingLoaderWannaBe);

        $cp = new DelegatingLoaderCloningCompilerPass();
        $cp->process($containerBuilder);

        \Phake::verify($containerBuilder)->setDefinition('modera_routing.symfony_delegating_loader', \Phake::capture($clonedDefinition));

        $this->assertInstanceOf(get_class($routingLoaderWannaBe), $clonedDefinition);
    }
}
