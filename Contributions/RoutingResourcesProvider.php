<?php

namespace Modera\FileUploaderBundle\Contributions;

use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2015 Modera Foundation
 */
class RoutingResourcesProvider implements ContributorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return array(
            '@ModeraFileUploaderBundle/Resources/config/routing.yml',
        );
    }
}
