<?php

namespace Modera\JSRuntimeIntegrationBundle\Contributions\Config;

use Modera\JSRuntimeIntegrationBundle\Config\ConfigMergerInterface;
use Modera\JSRuntimeIntegrationBundle\Menu\MenuManager;

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

    /**
     * @param array       $bundleConfig
     * @param MenuManager $menuMgr
     */
    public function __construct(array $bundleConfig, MenuManager $menuMgr)
    {
        $this->bundleConfig = $bundleConfig;
        $this->menuMgr = $menuMgr;
    }

    /**
     * {@inheritDoc}
     */
    public function merge(array $existingConfig)
    {
        $serializedMenuItems = array();
        foreach ($this->menuMgr->getAll() as $menuItem) {
            $serializedMenuItems[] =  array(
                'id' => $menuItem->getId(),
                'label' => $menuItem->getLabel(),
                'controller' => $menuItem->getController(),
                'metadata' => $menuItem->getMetadata()
            );
        }

        return array_merge($existingConfig, array(
            'homeSection' => $this->bundleConfig['home_section'],
            'menuItems' => $serializedMenuItems
        ));
    }
}