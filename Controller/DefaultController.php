<?php

namespace Modera\BackendDashboardBundle\Controller;

use Modera\BackendDashboardBundle\Dashboard\DashboardInterface;
use Modera\BackendDashboardBundle\Section\Section;
use Modera\FoundationBundle\Controller\AbstractBaseController;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Neton\DirectBundle\Annotation\Remote;

/**
 * @copyright 2013 Modera Foundation
 * @author    Alex Rudakov <alexandr.rudakov@modera.net>
 */
class DefaultController extends AbstractBaseController
{
    /**
     * @Remote
     *
     * @param array $params
     *
     * @return array
     */
    public function getDashboardsAction(array $params)
    {
        /* @var ContributorInterface $sectionsProvider */
        $dashboardProvider = $this->get('modera_backend_dashboard.dashboard_provider');

        $result = array();
        foreach ($dashboardProvider->getItems() as $dashboard) {
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

        return $result;
    }
}