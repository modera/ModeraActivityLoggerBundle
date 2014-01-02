<?php

namespace Modera\ServerCrudBundle\EntityFactory;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
interface EntityFactoryInterface
{
    /**
     * @param array $params
     * @param array $config
     *
     * @return object
     */
    public function create(array $params, array $config);
}