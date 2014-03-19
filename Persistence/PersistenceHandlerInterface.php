<?php

namespace Modera\ServerCrudBundle\Persistence;

/**
 * Implementations are responsible for persisting and querying data.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
interface PersistenceHandlerInterface
{
    /**
     * @param string $entityClass
     *
     * @return string[]
     */
    public function resolveEntityPrimaryKeyFields($entityClass);

    /**
     * @param object $entity
     *
     * @return OperationResult
     */
    public function save($entity);

    /**
     * @param object $entity
     *
     * @return OperationResult
     */
    public function update($entity);

    /**
     * @param string $entityClass
     * @param array  $params
     *
     * @return object[]
     */
    public function query($entityClass, array $params);

    /**
     * @param string $entityClass
     * @param array  $params
     *
     * @return OperationResult
     */
    public function remove($entityClass, array $params);

    /**
     * @param string $entityClass
     * @param array  $params
     *
     * @return integer
     */
    public function getCount($entityClass, array $params);
}