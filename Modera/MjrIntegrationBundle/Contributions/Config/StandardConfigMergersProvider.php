<?php

namespace Modera\MjrIntegrationBundle\Contributions\Config;

use Modera\MjrIntegrationBundle\Config\MainConfigInterface;
use Modera\MjrIntegrationBundle\DependencyInjection\ModeraMjrIntegrationExtension;
use Modera\MjrIntegrationBundle\Menu\MenuManager;
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
        $menuMgr = $container->get('modera_mjr_integration.menu.menu_manager');
        /* @var ContributorInterface $sectionsProvider */
        $sectionsProvider = $container->get('modera_mjr_integration.sections_provider');
        /* @var ContributorInterface $loaderMappingsProvider */
        $loaderMappingsProvider = $container->get('modera_mjr_integration.class_loader_mappings_provider');

        $bundleConfig = $container->getParameter(ModeraMjrIntegrationExtension::CONFIG_KEY);
        /* @var MainConfigInterface $mainConfig */
        $mainConfig = $container->get(
            $bundleConfig['main_config_provider']
        );

        $this->items = array(
            new ConfigMerger($mainConfig, $menuMgr, $sectionsProvider, $loaderMappingsProvider),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return $this->items;
    }
}
