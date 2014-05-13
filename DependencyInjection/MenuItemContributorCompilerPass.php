<?php

namespace Modera\MjrIntegrationBundle\DependencyInjection;

use Modera\MjrIntegrationBundle\Contributions\ConfigMenuItemsProvider;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class MenuItemContributorCompilerPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        $config = $container->getParameter(ModeraJSRuntimeIntegrationExtension::CONFIG_KEY);

        $def = new Definition(ConfigMenuItemsProvider::clazz(), array($config));
        $def->addTag('modera_mjr_integration.menu.menu_items_provider');

        $container->addDefinitions(array(
            'modera_js_runtime_integration.contributions.menu_items_provider' => $def
        ));
    }
} 