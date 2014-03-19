<?php

namespace Modera\ServerCrudBundle\Persistence;

/**
 * Implementations are responsible for formatting a model name for entity fully qualified class name that can be used
 * later on client side as well as resolving server entity class name by client model name.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
interface ModelManagerInterface
{
    /**
     * For fully qualified entity class name $entityClass must generate a string representation that later
     * can be used on client-side.
     *
     * @param string $entityClass
     *
     * @return string
     */
    public function generateModelIdFromEntityClass($entityClass);

    /**
     * For a client-side version of model name $modelId must resolve server-side fully qualified entity class
     * name.
     *
     * @param string $modelId
     *
     * @return string
     */
    public function generateEntityClassFromModelId($modelId);
}