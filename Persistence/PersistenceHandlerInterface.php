<?php

namespace Modera\AdminGeneratorBundle\Persistence;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
interface PersistenceHandlerInterface
{
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

    public function query(array $query);
}