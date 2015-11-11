<?php

namespace Modera\MjrIntegrationBundle\Contributions;

use Sli\ExpanderBundle\Ext\ContributorInterface;

class ClassLoaderMappingsProvider implements ContributorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return array(
            'Modera.mjrintegration' => '/bundles/moderamjrintegration/js',
        );
    }
}
