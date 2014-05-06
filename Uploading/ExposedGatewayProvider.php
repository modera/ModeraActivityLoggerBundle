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
     * @inheritDoc
     */
    public function getItems()
    {
        return $this->items;
    }

    static public function clazz()
    {
        return get_called_class();
    }
} 