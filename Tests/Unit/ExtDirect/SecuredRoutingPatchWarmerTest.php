<?php

namespace Modera\MJRSecurityIntegrationBundle\Tests\Unit\ExtDirect;

use Modera\MJRSecurityIntegrationBundle\ExtDirect\SecuredRoutingPatchWarmer;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2015 Modera Foundation
 */
class SecuredRoutingPatchWarmerTest extends \PHPUnit_Framework_TestCase
{
    public function testIsOptional()
    {
        $router = \Phake::mock('Symfony\Component\Routing\RouterInterface');

        $wm = new SecuredRoutingPatchWarmer($router, '');

        $this->assertFalse($wm->isOptional());
    }

    public function testWarmUp()
    {
        $newRoutePath = 'foo-new-route';

        $route = \Phake::mock('Symfony\Component\Routing\Route');

        $routeCol = \Phake::mock('Symfony\Component\Routing\RouteCollection');
        \Phake::when($routeCol)
            ->get('route')
            ->thenReturn($route)
        ;

        $router = \Phake::mock('Symfony\Component\Routing\RouterInterface');
        \Phake::when($router)
            ->getRouteCollection()
            ->thenReturn($routeCol)
        ;

        $wm = new SecuredRoutingPatchWarmer($router, $newRoutePath);

        $wm->warmUp('blah');

        \Phake::verify($route)->setPath($newRoutePath);
    }
}
