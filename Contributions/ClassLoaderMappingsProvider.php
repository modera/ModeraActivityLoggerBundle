<?php

namespace Modera\MJRSecurityIntegrationBundle\Contributions;

use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ClassLoaderMappingsProvider implements ContributorInterface
{
    private $items;

    public function __construct()
    {
        $this->items = array(
            'Modera.mjrsecurityintegration' => '/bundles/moderamjrsecurityintegration/js'
        );
    }

    /**
     * @inheritDoc
     */
    public function getItems()
    {
        return $this->items;
    }
}