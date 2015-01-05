<?php

namespace Modera\DirectBundle\Contributions;

use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
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
            '@ModeraDirectBundle/Resources/config/routing.yml',
        );
    }
}
