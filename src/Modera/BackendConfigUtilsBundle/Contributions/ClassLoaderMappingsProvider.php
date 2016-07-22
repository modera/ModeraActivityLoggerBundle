<?php

namespace Modera\BackendConfigUtilsBundle\Contributions;

use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ClassLoaderMappingsProvider implements ContributorInterface
{
    /**
     * @var string[]
     */
    private $items;

    public function __construct()
    {
        $this->items = array(
            'Modera.backend.configutils' => '/bundles/moderabackendconfigutils/js',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return $this->items;
    }
}
