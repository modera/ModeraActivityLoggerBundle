<?php

namespace Modera\ServerCrudBundle\Tests\Unit\Contributions;

use Modera\ServerCrudBundle\Contributions\ControllerActionInterceptorsProvider;
use Modera\ServerCrudBundle\Security\SecurityControllerActionsInterceptor;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ControllerActionInterceptorsProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetItems()
    {
        $ac = $this->getMock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');

        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container->expects($this->atLeastOnce())
                  ->method('get')
                  ->with($this->equalTo('security.authorization_checker'))
                  ->will($this->returnValue($ac));

        $provider = new ControllerActionInterceptorsProvider($container);

        $items = $provider->getItems();

        $this->assertEquals(1, count($items));
        $this->assertInstanceOf(SecurityControllerActionsInterceptor::clazz(), $items[0]);

        $items2 = $provider->getItems();

        // interceptors must be created only once
        $this->assertSame($items, $items2);
    }
}
