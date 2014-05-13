<?php

namespace Modera\MjrIntegrationBundle\Contributions;

use Modera\MjrIntegrationBundle\Menu\MenuItem;
use Modera\MjrIntegrationBundle\Menu\MenuItemInterface;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Sli\ExpanderBundle\Ext\OrderedContributorInterface;

/**
 * Contributes js-runtime menu items based on a config defined in "modera_mjr_integration" namespace.
 *
 * @see \Modera\MjrIntegrationBundle\DependencyInjection\Configuration
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ConfigMenuItemsProvider implements ContributorInterface
{
    private $items = array();

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        if (!isset($config['menu_items']) || !is_array($config['menu_items'])) {
            throw new \InvalidArgumentException('Given "$config" doesn\'t have key "menu_items" or it is not array!.');
        }

        foreach ($config['menu_items'] as $menuItem) {
            $controller = str_replace('$ns', $menuItem['namespace'], $menuItem['controller']);

            $this->items[] = new MenuItem($menuItem['name'], $controller, $menuItem['id'], array(
                MenuItemInterface::META_NAMESPACE => $menuItem['namespace'],
                MenuItemInterface::META_NAMESPACE_PATH => $menuItem['path']
            ));
        }
    }

    /**
     * @inheritDoc
     */
    public function getItems()
    {
        return $this->items;
    }

    static public function clazz()
    {
        return get_called_class();
    }
}