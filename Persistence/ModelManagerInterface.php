<?php

namespace Modera\AdminGeneratorBundle\Persistence;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
interface ModelManagerInterface
{
    /**
     * @param string $entityClass
     *
     * @return string
     */
    public function generateModelIdFromEntityClass($entityClass);

    /**
     * @param string $modelId
     *
     * @return string
     */
    public function generateEntityClassFromModelId($modelId);
}