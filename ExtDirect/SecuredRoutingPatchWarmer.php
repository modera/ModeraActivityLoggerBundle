<?php

namespace Modera\MJRSecurityIntegrationBundle\ExtDirect;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * MPFE-712.
 *
 * @private
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2015 Modera Foundation
 */
class SecuredRoutingPatchWarmer implements CacheWarmerInterface
{
    private $router;
    private $newRoutePath;

    public function __construct(RouterInterface $router, $newRoutePath)
    {
        $this->router = $router;
        $this->newRoutePath = $newRoutePath;
    }

    /**
     * {@inheritdoc}
     */
    public function isOptional()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp($cacheDir)
    {
        $col = $this->router->getRouteCollection();

        $directRoute = $col->get('route');

        $hasDirectBundle = null !== $directRoute;
        if ($hasDirectBundle) {
            $directRoute->setPath($this->newRoutePath);
        }
    }
}
