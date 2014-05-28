<?php

namespace Modera\MJRCacheAwareClassLoaderBundle\Contributions;

use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class JsResourcesProvider implements ContributorInterface
{
    private $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @inheritDoc
     */
    public function getItems()
    {
        return array(
            $this->router->generate('modera_mjr_cache_aware_class_loader')
        );
    }
} 