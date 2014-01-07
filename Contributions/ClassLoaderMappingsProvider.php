<?php

namespace Modera\SecurityAwareJSRuntimeBundle\Contributions;

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
            'Modera.securityawarejsruntime' => '/bundles/moderasecurityawarejsruntime/js'
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