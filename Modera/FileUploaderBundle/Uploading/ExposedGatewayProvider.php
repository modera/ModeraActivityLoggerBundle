<?php

namespace Modera\FileUploaderBundle\Uploading;

use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ExposedGatewayProvider implements ContributorInterface
{
    private $items;

    /**
     * @param AllExposedRepositoriesGateway $gateway
     */
    public function __construct(AllExposedRepositoriesGateway $gateway)
    {
        $this->items = array($gateway);
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return $this->items;
    }

    public static function clazz()
    {
        return get_called_class();
    }
}
