<?php

namespace Modera\SecurityAwareJSRuntimeBundle\Contributions;

use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class RoutingResourcesProvider implements ContributorInterface
{
    /**
     * @inheritDoc
     */
    public function getItems()
    {
        return array(
            '@ModeraSecurityAwareJSRuntimeBundle/Resources/config/routing.yml'
        );
    }
}
