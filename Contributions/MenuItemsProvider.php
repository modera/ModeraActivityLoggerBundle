<?php

namespace Modera\BackendDashboardBundle\Contributions;

use Modera\MjrIntegrationBundle\Menu\MenuItem;
use Modera\MjrIntegrationBundle\Menu\MenuItemInterface;
use Modera\MjrIntegrationBundle\Model\FontAwesome;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Sli\ExpanderBundle\Ext\OrderedContributorInterface;

/**
 * Contributes js-runtime menu items.
 *
 * @author    Alex Rudakov <alexandr.rudakov@modera.org>
 * @copyright 2014 Modera Foundation
 */
class MenuItemsProvider implements OrderedContributorInterface
{
    private $items;

    /**
     * Registers dashboard item as a section tab in Backend
     */
    public function __construct()
    {
        $this->items = array(
            new MenuItem('Dashboard', 'Modera.backend.dashboard.runtime.Section', 'dashboard', [
                MenuItemInterface::META_NAMESPACE => 'Modera.backend.dashboard',
                MenuItemInterface::META_NAMESPACE_PATH => '/bundles/moderabackenddashboard/js'
            ], FontAwesome::resolve('dashboard')),
        );
    }

    /**
     * @inheritDoc
     *
     * @return MenuItemsProvider[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Makes dashboard a first tab
     *
     * @return int
     */
    public function getOrder()
    {
        return 0;
    }
}