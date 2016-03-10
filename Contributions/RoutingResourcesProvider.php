<?php

namespace Modera\SecurityBundle\Contributions;

use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
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
            array(
                'resource' => '@ModeraSecurityBundle/Controller/SecurityController.php',
                'type' => 'annotation',
            ),
        );
    }
}
