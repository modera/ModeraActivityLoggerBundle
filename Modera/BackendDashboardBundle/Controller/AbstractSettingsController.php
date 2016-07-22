<?php


namespace Modera\BackendDashboardBundle\Controller;

use Modera\BackendDashboardBundle\Dashboard\DashboardInterface;
use Modera\BackendDashboardBundle\Entity\SettingsEntityInterface;
use Modera\ServerCrudBundle\Controller\AbstractCrudController;
use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author    Alex Rudakov <alexandr.rudakov@modera.org>
 * @copyright 2014 Modera Foundation
 */
abstract class AbstractSettingsController extends AbstractCrudController
{
    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        $me = $this;

        return array(
            'entity' => $this->getEntityClass(),
            'hydration' => array(
                'groups' => array(
                    'main' => function($settings) use($me) {
                            return $me->hydrateSettings($settings);
                        }
                ),
                'profiles' => array(
                    'main'
                )
            ),
            'map_data_on_update' => function($params, $entity, $defaultMapper) use ($me) {
                    $me->mapEntityOnUpdate($params, $entity, $defaultMapper);
                }
        );
    }

    /**
     * @return string
     */
    abstract protected function getEntityClass();

    private function mapEntityOnUpdate(array $params, SettingsEntityInterface $entity)
    {
        if (isset($params['dashboards']) && is_array($params['dashboards'])) {
            $dashboardSettings = array(
                'hasAccess' => array(),
                'defaultDashboard' => null
            );

            foreach ($params['dashboards'] as $dashboard) {
                if (isset($dashboard['hasAccess']) && isset($dashboard['id']) && isset($dashboard['isDefault'])) {
                    if (true === $dashboard['isDefault']) {
                        $dashboardSettings['hasAccess'][] = $dashboard['id'];
                        $dashboardSettings['defaultDashboard'] = $dashboard['id'];

                        continue;
                    }

                    if (true === $dashboard['hasAccess']) {
                        $dashboardSettings['hasAccess'][] = $dashboard['id'];
                    }
                }
            }

            $entity->setDashboardSettings($dashboardSettings);
        }
    }

    /**
     * @return ContributorInterface
     */
    private function getDashboardProvider()
    {
        return $this->get('modera_backend_dashboard.dashboard_provider');
    }

    private function hydrateSettings(SettingsEntityInterface $e)
    {
        $dashboards = array();
        foreach ($this->getDashboardProvider()->getItems() as $dashboard) {
            /* @var DashboardInterface $dashboard */

            if (!$dashboard->isAllowed($this->container)) {
                continue;
            }

            $dashboards[] = array(
                'id' => $dashboard->getName(),
                'name' => $dashboard->getLabel()
            );
        }

        $userDashboardSettings = $e->getDashboardSettings();

        $preparedDashboardSettings = array();
        foreach ($dashboards as $dashboard) {
            $preparedDashboardSettings[] = array_merge(
                $dashboard,
                array(
                    'hasAccess' => in_array($dashboard['id'], $userDashboardSettings['hasAccess']),
                    'isDefault' => $dashboard['id'] == $userDashboardSettings['defaultDashboard']
                )
            );
        }

        return array(
            'id' => $e->getId(),
            'title' => $e->describeEntity(),
            'dashboardSettings' => $preparedDashboardSettings
        );
    }
}