<?php

namespace Modera\ServerCrudBundle\DataMapping;

/**
 * Implementations are responsible for taking dta coming from client side and mapping it onto entities so eventually
 * then can be persisted to database.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
interface DataMapperInterface
{
    /**
     * Should bind $params onto given $entity.
     *
     * @param array  $params
     * @param object $entity
     */
    public function mapData(array $params, $entity);
}
