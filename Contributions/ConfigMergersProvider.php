<?php

namespace Modera\BackendDashboardBundle\Contributions;

use Modera\BackendDashboardBundle\Dashboard\DashboardInterface;
use Modera\JSRuntimeIntegrationBundle\Config\ConfigMergerInterface;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Modera\JSRuntimeIntegrationBundle\Config\CallbackConfigMerger;

/**
 * Adds dashboard list to config for backend. It allows
 * to show dashboards immediately without loading remote data through Direct.
 *
 * @author    Alex Rudakov <alexandr.rudakov@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ConfigMergersProvider implements ContributorInterface, ConfigMergerInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var \Sli\ExpanderBundle\Ext\ContributorInterface
     */
    private $dashboardProvider;

    /**
     * @param ContainerInterface   $container         Symfony container for isAllowed() method
     * @param ContributorInterface $dashboardProvider Dashboard providers are collected automatically by Expander bundle
     */
    public function __construct(ContainerInterface $container, ContributorInterface $dashboardProvider)
    {
        $this->container = $container;
        $this->dashboardProvider = $dashboardProvider;
    }

    /**
     * Merge in dashboard list into runtime configuration.
     *
     * @param array $currentConfig
     *
     * @return array
     */
    public function merge(array $currentConfig)
    {
        $result = array();
        foreach ($this->dashboardProvider->getItems() as $dashboard) {
            /* @var DashboardInterface $dashboard */

            if (!$dashboard->isAllowed($this->container)) {
                continue;
            }

            $result[] = array(
                'name' => $dashboard->getName(),
                'label' => $dashboard->getLabel(),
                'uiClass' => $dashboard->getUiClass(),
                'default' => false
            );
        }

        if (count($result)) {
            $result[0]['default'] = true;
        }

        return array_merge($currentConfig, array(
                'modera_backend_dashboard' => array(
                    'dashboards' => $result
                )
            ));
    }

    /**
     * @inheritDoc
     *
     * @return array
     */
    public function getItems()
    {
        return array($this);
    }

    /**
     * Return container
     *
     * @return mixed
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Return dashboardProvider
     *
     * @return mixed
     */
    public function getDashboardProvider()
    {
        return $this->dashboardProvider;
    }
}