<?php

namespace Modera\ServerCrudBundle\EntityFactory;

/**
 * Implementations of this interface are responsible for creating instances of objects that later will be used
 * to validate and persist data to database.
 *
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