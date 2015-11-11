<?php

namespace Modera\MJRSecurityIntegrationBundle\ExtDirect;

/**
 * MPFE-712.
 *
 * Patches existing route so it would be placed behind a firewall.
 *
 * @private
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2015 Modera Foundation
 */
class RouteFormatter
{
    private $backendRoutesPrefix;

    /**
     * @param string $backendRoutesPrefix
     */
    public function __construct($backendRoutesPrefix)
    {
        $this->backendRoutesPrefix = $backendRoutesPrefix;
    }

    /**
     * @param $routePathToPrefix
     *
     * @return string
     */
    public function format($routePathToPrefix)
    {
        $sanitizedRoutesPrefix = $this->backendRoutesPrefix;
        if ('/' == $this->backendRoutesPrefix{strlen($this->backendRoutesPrefix) - 1}) {
            $sanitizedRoutesPrefix = substr($this->backendRoutesPrefix, 0, strlen($this->backendRoutesPrefix) - 1);
        }

        $sanitizedRoutePath = $routePathToPrefix;
        if ('/' == $routePathToPrefix{0}) {
            $sanitizedRoutePath = substr($routePathToPrefix, 1);
        }

        return $sanitizedRoutesPrefix.'/'.$sanitizedRoutePath;
    }
}
