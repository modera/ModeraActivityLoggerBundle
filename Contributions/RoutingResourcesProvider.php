<?php

namespace Modera\MJRSecurityIntegrationBundle\Contributions;

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
            '@ModeraMJRSecurityIntegrationBundle/Resources/config/routing.yml'
        );
    }
}
