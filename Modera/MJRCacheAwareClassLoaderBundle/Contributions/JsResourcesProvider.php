<?php

namespace Modera\MJRCacheAwareClassLoaderBundle\Contributions;

use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Contributes a javascript link to dynamically generated extjs-class-loader overriding logic.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class JsResourcesProvider implements ContributorInterface
{
    private $urlGenerator;

    /**
     * @param RouterInterface $router
     */
    public function __construct(UrlGeneratorInterface $router)
    {
        $this->urlGenerator = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return array(
            $this->urlGenerator->generate('modera_mjr_cache_aware_class_loader'),
        );
    }
}
