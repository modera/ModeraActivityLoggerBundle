<?php

namespace Modera\JSRuntimeIntegrationBundle\Contributions\Config;

use Modera\JSRuntimeIntegrationBundle\DependencyInjection\ModeraJSRuntimeIntegrationExtension;
use Modera\JSRuntimeIntegrationBundle\Menu\MenuManager;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides standard configurators.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class StandardConfigMergersProvider implements ContributorInterface
{
    private $items;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        /* @var MenuManager $menuMgr */
        $menuMgr = $container->get('mf.jsruntimeintegration.menu.menu_manager');

        $this->items = array(
            new ConfigMerger($container->getParameter(ModeraJSRuntimeIntegrationExtension::CONFIG_KEY), $menuMgr)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getItems()
    {
        return $this->items;
    }
}