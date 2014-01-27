<?php

namespace Modera\JSRuntimeIntegrationBundle\Contributions\Config;

use Modera\JSRuntimeIntegrationBundle\Config\ConfigMergerInterface;
use Modera\JSRuntimeIntegrationBundle\Menu\MenuManager;
use Modera\JSRuntimeIntegrationBundle\Sections\Section;
use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * Merges standard and very basic configuration.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class ConfigMerger implements ConfigMergerInterface
{
    private $bundleConfig;
    private $menuMgr;
    private $sectionsProvider;
    private $classLoaderMappingsProvider;

    /**
     * @param array       $bundleConfig
     * @param MenuManager $menuMgr
     */
    public function __construct(
        array $bundleConfig, MenuManager $menuMgr, ContributorInterface $sectionsProvider, ContributorInterface $classLoaderMappingsProvider
    )
    {
        $this->bundleConfig = $bundleConfig;
        $this->menuMgr = $menuMgr;
        $this->sectionsProvider = $sectionsProvider;
        $this->classLoaderMappingsProvider = $classLoaderMappingsProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function merge(array $existingConfig)
    {
        $menuItems = array();
        foreach ($this->menuMgr->getAll() as $menuItem) {
            $menuItems[] =  array(
                'id' => $menuItem->getId(),
                'glyph' => $menuItem->getGlyph(),
                'label' => $menuItem->getLabel(),
                'controller' => $menuItem->getController(),
                'metadata' => $menuItem->getMetadata()
            );
        }

        $sections = array();
        foreach ($this->sectionsProvider->getItems() as $section) {
            /* @var Section $section */
            $sections[] = array(
                'id' => $section->getId(),
                'controller' => $section->getController(),
                'metadata' => $section->getMetadata()
            );
        }

        return array_merge($existingConfig, array(
            'deploymentName' => $this->bundleConfig['deployment_name'],
            'deploymentUrl' => $this->bundleConfig['deployment_url'],
            'homeSection' => $this->bundleConfig['home_section'],
            'sections' => $sections, // backend sections
            'menuItems' => $menuItems,
            'classLoaderMappings' => $this->classLoaderMappingsProvider->getItems()
        ));
    }
}