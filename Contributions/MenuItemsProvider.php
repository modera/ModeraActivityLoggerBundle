<?php

namespace Modera\BackendToolsBundle\Contributions;

use Modera\JSRuntimeIntegrationBundle\Menu\MenuItem;
use Modera\JSRuntimeIntegrationBundle\Menu\MenuItemInterface;
use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * Contributes js-runtime menu items.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class MenuItemsProvider implements ContributorInterface
{
    private $items;

    public function __construct()
    {
        $this->items = array(
            new MenuItem('Tools', 'Modera.backend.tools.runtime.Section', 'tools', [
                MenuItemInterface::META_NAMESPACE => 'Modera.backend.tools',
                MenuItemInterface::META_NAMESPACE_PATH => '/bundles/moderabackendtools/js'
            ], 'xe807@mf-theme-header-icon'),
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