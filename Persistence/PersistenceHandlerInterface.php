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
     * Must returns field names which can be used to uniquely identify a record.
     *
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
     * @param object[] $entities
     *
     * @return OperationResult
     */
    public function updateBatch(array $entities);

    /**
     * @param string $entityClass
     * @param array  $params
     *
     * @return object[]
     */
    public function query($entityClass, array $params);

    /**
     * @param object[] $entities
     *
     * @return OperationResult
     */
    public function remove(array $entities);

    /**
     * @param string $entityClass
     * @param array  $params
     *
     * @return integer
     */
    public function getCount($entityClass, array $params);
}