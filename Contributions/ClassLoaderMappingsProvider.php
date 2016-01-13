<?php

namespace Modera\DynamicallyConfigurableMJRBundle\Contributions;

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
            'Modera.backend.dcmjr' => '/bundles/moderadynamicallyconfigurablemjr/js',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return string
     */
    public static function clazz()
    {
        return get_called_class();
    }
}
