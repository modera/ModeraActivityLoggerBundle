<?php

namespace Modera\BackendToolsBundle\Contributions;

use Modera\BackendToolsBundle\ModeraBackendToolsBundle;
use Modera\MjrIntegrationBundle\Menu\MenuItem;
use Modera\MjrIntegrationBundle\Menu\MenuItemInterface;
use Modera\MjrIntegrationBundle\Model\FontAwesome;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Contributes js-runtime menu items.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class MenuItemsProvider implements ContributorInterface
{
    private $securityContext;

    private $items;

    private $tabOrder;

    /**
     * Set params and services
     *
     * @param SecurityContextInterface $securityContext
     * @param int $tabOrder
     */
    public function __construct(SecurityContextInterface $securityContext, $tabOrder)
    {
        $this->tabOrder = $tabOrder;
        $this->securityContext = $securityContext;
    }

    /**
     * @inheritDoc
     */
    public function getItems()
    {
        if (!$this->items) {
            $this->items = [];

            if ($this->securityContext->isGranted(ModeraBackendToolsBundle::ROLE_ACCESS_TOOLS_SECTION)) {
                $this->items[] = new MenuItem('Tools', 'Modera.backend.tools.runtime.Section', 'tools', array(
                    MenuItemInterface::META_NAMESPACE => 'Modera.backend.tools',
                    MenuItemInterface::META_NAMESPACE_PATH => '/bundles/moderabackendtools/js'
                ), FontAwesome::resolve('wrench'));
            }
        }

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