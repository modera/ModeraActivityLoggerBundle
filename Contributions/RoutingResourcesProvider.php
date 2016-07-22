<?php

namespace Modera\MJRCacheAwareClassLoaderBundle\Contributions;

use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class RoutingResourcesProvider implements ContributorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return array(
            '@ModeraMJRCacheAwareClassLoaderBundle/Resources/config/routing.yml',
        );
    }
}
