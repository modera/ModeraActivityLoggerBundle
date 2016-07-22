<?php

namespace Modera\MJRThemeIntegrationBundle\Contributions;

use Modera\MjrIntegrationBundle\DependencyInjection\ModeraMjrIntegrationExtension;
use Modera\MJRThemeIntegrationBundle\DependencyInjection\ModeraMJRThemeIntegrationExtension;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class CssResourcesProvider implements ContributorInterface
{
    private $themeIntegrationConfig;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->themeIntegrationConfig = $container->getParameter(ModeraMJRThemeIntegrationExtension::CONFIG_KEY);
        $this->mjrInteggrationConfig = $container->getParameter(ModeraMjrIntegrationExtension::CONFIG_KEY);
    }

    /**
     * @inheritDoc
     */
    public function getItems()
    {
        return array(
            $this->themeIntegrationConfig['theme_path'] . '/build/resources/modera-theme-all-debug.css',
            $this->mjrInteggrationConfig['runtime_path'] . '/build/resources/MJR-all-debug.css'
        );
    }
}