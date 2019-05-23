<?php

namespace Modera\ActivityLoggerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Adds a service with ID "modera_activity_logger.manager.activity_manager" to service container
 * that you can use in your application logic without the need to use specific implementation.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ServiceAliasCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $config = $container->getParameter(ModeraActivityLoggerExtension::CONFIG_KEY);

        $container->setAlias('modera_activity_logger.manager.activity_manager', $config['activity_manager']);
        $container->getAlias('modera_activity_logger.manager.activity_manager')->setPublic(true);
    }
}
