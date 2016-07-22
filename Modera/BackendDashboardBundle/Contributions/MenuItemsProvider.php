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

    private $tabOrder;

    /**
     * Registers dashboard item as a section tab in Backend
     *
     * @param $tabOrder
     */
    public function __construct($tabOrder)
    {
        $this->tabOrder = $tabOrder;

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
     * Return tab order
     *
     * @return int
     */
    public function getOrder()
    {
        return $this->tabOrder;
    }
}