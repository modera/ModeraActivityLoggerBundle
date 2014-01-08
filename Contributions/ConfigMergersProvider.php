<?php

namespace Modera\BackendDashboardBundle\Contributions;

use Modera\BackendDashboardBundle\Dashboard\DashboardInterface;
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
class ConfigMergersProvider implements ContributorInterface
{
    private $items;

    /**
     * @param ContainerInterface   $container
     * @param ContributorInterface $dashboardProvider
     */
    public function __construct(ContainerInterface $container, ContributorInterface $dashboardProvider)
    {
        $this->items = array(
            new CallbackConfigMerger(function(array $currentConfig) use ($container, $dashboardProvider) {

                $result = array();
                foreach ($dashboardProvider->getItems() as $dashboard) {
                    /* @var DashboardInterface $dashboard */

                    if (!$dashboard->isAllowed($container)) {
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
                    'dashboards' => $result
                ));
            })
        );
    }

    /**
     * @inheritDoc
     *
     * return CallbackConfigMerger[]
     */
    public function getItems()
    {
        return $this->items;
    }
}