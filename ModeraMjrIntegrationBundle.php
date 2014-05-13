<?php

namespace Modera\MjrIntegrationBundle;

use Modera\MjrIntegrationBundle\DependencyInjection\MenuItemContributorCompilerPass;
use Sli\ExpanderBundle\DependencyInjection\CompositeContributorsProviderCompilerPass;
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
        // lets to contribute implementations of \Modera\MjrIntegrationBundle\Config\ConfigMergerInterface
        // that will be used to prepare runtime-config
        $container->addCompilerPass(
            new CompositeContributorsProviderCompilerPass('modera_mjr_integration.config.config_mergers_provider')
        );

        // allows to contribute menu items by defining them in config file
        $container->addCompilerPass(new MenuItemContributorCompilerPass());

        // allows to contribute sections that will be displayed in backend menu
        $container->addCompilerPass(
            new CompositeContributorsProviderCompilerPass('modera_mjr_integration.menu.menu_items_provider')
        );

        // client side dependency injection container services providers
        $container->addCompilerPass(
            new CompositeContributorsProviderCompilerPass('modera_mjr_integration.csdi.service_definitions_provider')
        );

        // contributes section which will be possible to activate ( see js class MF.runtime.Section )
        $container->addCompilerPass(
            new CompositeContributorsProviderCompilerPass('modera_mjr_integration.sections_provider')
        );

        // CSS files to include in a page where the runtime will be bootstrapped
        $container->addCompilerPass(
            new CompositeContributorsProviderCompilerPass('modera_mjr_integration.css_resources_provider')
        );

        // allows to add javascript class loader mappings. getItems() must return result which which has the following
        // structure:
        // array(
        //  'foonamespace' => 'bundles/foopath',
        //  'barnamespace' => 'bundles/barpath'
        // )
        //
        $container->addCompilerPass(
            new CompositeContributorsProviderCompilerPass('modera_mjr_integration.class_loader_mappings_provider')
        );

        $container->addCompilerPass(
            new CompositeContributorsProviderCompilerPass('modera_mjr_integration.js_resources_provider')
        );
    }
}
