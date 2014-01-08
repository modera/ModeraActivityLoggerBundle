<?php

namespace Modera\BackendDashboardBundle\Contributions;

use Modera\BackendDashboardBundle\Dashboard\SimpleDashboard;
use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * Contributes js-runtime menu items.
 *
 * @copyright 2013 Modera Foundation
 * @author    Alex Rudakov <alexandr.rudakov@modera.net>
 */
class DashboardProvider implements ContributorInterface
{
    private $items;

    public function __construct()
    {
        $this->items = array(
            new SimpleDashboard('default', 'Default dashboard', 'Modera.backend.dashboard.view.DefaultDashboard'),
            new SimpleDashboard('motivation', 'Motivation dashboard', 'Modera.backend.dashboard.view.MotivationDashboard'),
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