<?php

namespace Modera\MJRSecurityIntegrationBundle\Tests\Unit\ExtDirect;

use Modera\MJRSecurityIntegrationBundle\ExtDirect\ParamUpdaterCompilerPass;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2015 Modera Foundation
 */
class ParamUpdaterCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $backendRoutesPrefix = 'mega-backend';
        $routePattern = 'route-pattern';

        $builder = \Phake::mock('Symfony\Component\DependencyInjection\ContainerBuilder');
        \Phake::when($builder)
            ->getParameter('modera_mjr_integration.route_prefix')
            ->thenReturn($backendRoutesPrefix)
        ;
        \Phake::when($builder)
            ->getParameter('direct.api.route_pattern')
            ->thenReturn($routePattern)
        ;

        $cp = new ParamUpdaterCompilerPass();

        $cp->process($builder);

        \Phake::verify($builder)->setParameter('direct.api.route_pattern', $backendRoutesPrefix.'/'.$routePattern);
    }
}
