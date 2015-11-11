<?php

namespace Modera\MJRSecurityIntegrationBundle\ExtDirect;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * MPFE-712.
 *
 * @private
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2015 Modera Foundation
 */
class ParamUpdaterCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $backendRoutesPrefix = $container->getParameter('modera_mjr_integration.route_prefix');
        $formatter = new RouteFormatter($backendRoutesPrefix);

        $paramName = 'direct.api.route_pattern';

        $directRoutePattern = $container->getParameter($paramName);
        $container->setParameter($paramName, $formatter->format($directRoutePattern));
    }

    /**
     * @return string
     */
    public static function clazz()
    {
        return get_called_class();
    }
}
