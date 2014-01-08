<?php

namespace Modera\BackendDashboardBundle\Contributions;

use Modera\BackendDashboardBundle\Dashboard\DashboardInterface;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Modera\JSRuntimeIntegrationBundle\Config\CallbackConfigMerger;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ConfigMergersProvider implements ContributorInterface
{
    private $items;

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
     */
    public function getItems()
    {
        return $this->items;
    }
}