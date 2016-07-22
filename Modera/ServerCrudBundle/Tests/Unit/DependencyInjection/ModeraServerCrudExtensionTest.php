<?php

namespace Modera\ServerCrudBundle\Tests\Unit\DependencyInjection;

use Modera\ServerCrudBundle\DependencyInjection\ModeraServerCrudExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ModeraServerCrudExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testIfItHasServicesTagged()
    {
        $ext = new ModeraServerCrudExtension();
        $container = new ContainerBuilder();

        $ext->load(array(), $container);

        $this->assertEquals(1, count($container->findTaggedServiceIds('modera_server_crud.intercepting.cai_provider')));
    }
}
