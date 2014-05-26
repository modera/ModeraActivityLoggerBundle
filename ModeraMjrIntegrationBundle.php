<?php

namespace Modera\MjrIntegrationBundle;

use Modera\MjrIntegrationBundle\DependencyInjection\MenuItemContributorCompilerPass;
use Sli\ExpanderBundle\DependencyInjection\CompositeContributorsProviderCompilerPass;
use Sli\ExpanderBundle\Ext\ExtensionPoint;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Bundle ships basic utilities which simplify integration of Modera JavaScript runtime ( MJR ).
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class ModeraMjrIntegrationBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        $configMergersProvider = new ExtensionPoint('modera_mjr_integration.config.config_mergers_provider');
        $configMergersProvider->setDescription(
            'Lets to contribute implementations of ConfigMergerInterface that will be used to prepare runtime-config.'
        );
        $container->addCompilerPass($configMergersProvider->createCompilerPass());

        // allows to contribute menu items by defining them in config file
        $container->addCompilerPass(new MenuItemContributorCompilerPass());

        $menuItemsProvider = new ExtensionPoint('modera_mjr_integration.menu.menu_items_provider');
        $menuItemsProvider->setDescription('Allows to contribute sections that will be displayed in backend menu.');
        $container->addCompilerPass($menuItemsProvider->createCompilerPass());

        $serviceDefinitionsProvider = new ExtensionPoint('modera_mjr_integration.csdi.service_definitions_provider');
        $serviceDefinitionsProvider->setDescription('client side dependency injection container services providers.');
        $container->addCompilerPass($serviceDefinitionsProvider->createCompilerPass());

        $sectionsProvider = new ExtensionPoint('modera_mjr_integration.sections_provider');
        $sectionsProvider->setDescription('Contributes section which will be possible to activate ( see js class MF.runtime.Section).');
        $container->addCompilerPass($sectionsProvider->createCompilerPass());

        $cssResourcesProvider = new ExtensionPoint('modera_mjr_integration.css_resources_provider');
        $cssResourcesProvider->setDescription('CSS files to include in a page where the runtime will be bootstrapped');
        $container->addCompilerPass($cssResourcesProvider->createCompilerPass());

        $classLoaderMappingsProvider = new ExtensionPoint('modera_mjr_integration.class_loader_mappings_provider');
        $classLoaderMappingsProvider->setDescription(
            'Allows to add javascript class loader mappings. getItems() must return result which which has the following structure: array("ns" => "path")'
        );
        $container->addCompilerPass($classLoaderMappingsProvider->createCompilerPass());

        $jsResourcesProvider = new ExtensionPoint('modera_mjr_integration.js_resources_provider');
        $jsResourcesProvider->setDescription('Allows to contribute JS resources that will be used in Backend.');
        $container->addCompilerPass($jsResourcesProvider->createCompilerPass());
    }
}
