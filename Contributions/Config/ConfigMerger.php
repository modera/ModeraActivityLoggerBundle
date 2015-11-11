<?php

namespace Modera\MjrIntegrationBundle\Contributions\Config;

use Modera\MjrIntegrationBundle\Config\ConfigMergerInterface;
use Modera\MjrIntegrationBundle\Config\MainConfigInterface;
use Modera\MjrIntegrationBundle\Menu\MenuManager;
use Modera\MjrIntegrationBundle\Sections\Section;
use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * Merges standard and very basic configuration.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class ConfigMerger implements ConfigMergerInterface
{
    private $mainConfig;
    private $menuMgr;
    private $sectionsProvider;
    private $classLoaderMappingsProvider;

    /**
     * @param array       $mainConfig
     * @param MenuManager $menuMgr
     */
    public function __construct(
        MainConfigInterface $mainConfig, MenuManager $menuMgr, ContributorInterface $sectionsProvider, ContributorInterface $classLoaderMappingsProvider
    ) {
        $this->mainConfig = $mainConfig;
        $this->menuMgr = $menuMgr;
        $this->sectionsProvider = $sectionsProvider;
        $this->classLoaderMappingsProvider = $classLoaderMappingsProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(array $existingConfig)
    {
        $menuItems = array();
        foreach ($this->menuMgr->getAll() as $menuItem) {
            $menuItems[] = array(
                'id' => $menuItem->getId(),
                'glyph' => $menuItem->getGlyph(),
                'label' => $menuItem->getLabel(),
                'controller' => $menuItem->getController(),
                'metadata' => $menuItem->getMetadata(),
            );
        }

        $sections = array();
        foreach ($this->sectionsProvider->getItems() as $section) {
            /* @var Section $section */
            $sections[] = array(
                'id' => $section->getId(),
                'controller' => $section->getController(),
                'metadata' => $section->getMetadata(),
            );
        }

        return array_merge($existingConfig, array(
            'deploymentName' => $this->mainConfig->getTitle(),
            'deploymentUrl' => $this->mainConfig->getUrl(),
            'homeSection' => $this->mainConfig->getHomeSection(),
            'sections' => $sections, // backend sections
            'menuItems' => $menuItems,
            'classLoaderMappings' => $this->classLoaderMappingsProvider->getItems(),
        ));
    }
}
