<?php

namespace Modera\BackendToolsBundle\Contributions;

use Modera\BackendToolsBundle\ModeraBackendToolsBundle;
use Modera\MjrIntegrationBundle\Menu\MenuItem;
use Modera\MjrIntegrationBundle\Menu\MenuItemInterface;
use Modera\MjrIntegrationBundle\Model\FontAwesome;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Contributes js-runtime menu items.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class MenuItemsProvider implements ContributorInterface
{
    private $authorizationChecker;

    private $items;

    private $tabOrder;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param int $tabOrder
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker, $tabOrder)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->tabOrder = $tabOrder;
    }

    /**
     * @inheritDoc
     */
    public function getItems()
    {
        if (!$this->items) {
            $this->items = [];

            if ($this->authorizationChecker->isGranted(ModeraBackendToolsBundle::ROLE_ACCESS_TOOLS_SECTION)) {
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