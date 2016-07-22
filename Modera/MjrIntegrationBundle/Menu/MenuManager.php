<?php

namespace Modera\MjrIntegrationBundle\Menu;

use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * Manages menu.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class MenuManager
{
    private $provider;

    /**
     * @param ContributorInterface $provider
     */
    public function __construct(ContributorInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @return MenuItemInterface[]
     */
    public function getAll()
    {
        return $this->provider->getItems();
    }
}
