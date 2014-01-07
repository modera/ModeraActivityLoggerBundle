<?php

namespace Modera\BackendDashboardBundle\Contributions;

use Modera\JSRuntimeIntegrationBundle\Menu\MenuItem;
use Modera\JSRuntimeIntegrationBundle\Menu\MenuItemInterface;
use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * Contributes js-runtime menu items.
 *
 * @copyright 2013 Modera Foundation
 * @author    Alex Rudakov <alexandr.rudakov@modera.net>
 */
class MenuItemsProvider implements ContributorInterface
{
    private $items;

    public function __construct()
    {
        $this->items = array(
            new MenuItem('Dashboard', 'Modera.backend.dashboard.runtime.Section', 'dashboard', [
                MenuItemInterface::META_NAMESPACE => 'Modera.backend.dashboard',
                MenuItemInterface::META_NAMESPACE_PATH => '/bundles/moderabackenddashboard/js'
            ]),
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