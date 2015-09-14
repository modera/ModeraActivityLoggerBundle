<?php

namespace Modera\MjrIntegrationBundle\Contributions;

use Sli\ExpanderBundle\Ext\ContributorInterface;

class ClassLoaderMappingsProvider implements ContributorInterface
{
    /**
     * @inheritDoc
     */
    public function getItems()
    {
        return array(
            'Modera.mjrintegration' => '/bundles/moderamjrintegration/js'
        );
    }
}