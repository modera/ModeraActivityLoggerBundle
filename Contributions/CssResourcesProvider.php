<?php

namespace Modera\MjrIntegrationBundle\Contributions;

use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class CssResourcesProvider implements ContributorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return array(
            '//netdna.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css',
        );
    }
}
