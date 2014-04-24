<?php

namespace Modera\JSRuntimeIntegrationBundle\Contributions\Config;

use Modera\JSRuntimeIntegrationBundle\Config\MainConfigInterface;
use Modera\JSRuntimeIntegrationBundle\DependencyInjection\ModeraJSRuntimeIntegrationExtension;
use Modera\JSRuntimeIntegrationBundle\Menu\MenuManager;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides standard configurators.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class StandardConfigMergersProvider implements ContributorInterface
{
    private $items;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        /* @var MenuManager $menuMgr */
        $menuMgr = $container->get('mf.jsruntimeintegration.menu.menu_manager');
        /* @var ContributorInterface $sectionsProvider */
        $sectionsProvider = $container->get('mf.jsruntimeintegration.sections_provider');
        /* @var ContributorInterface $loaderMappingsProvider */
        $loaderMappingsProvider = $container->get('mf.jsruntimeintegration.class_loader_mappings_provider');

        $bundleConfig = $container->getParameter(ModeraJSRuntimeIntegrationExtension::CONFIG_KEY);
        /* @var MainConfigInterface $mainConfig */
        $mainConfig = $container->get(
            $bundleConfig['main_config_provider']
        );

        $this->items = array(
            new ConfigMerger($mainConfig, $menuMgr, $sectionsProvider, $loaderMappingsProvider)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getItems()
    {
        return $this->items;
    }
}