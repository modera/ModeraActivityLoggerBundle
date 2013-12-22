<?php

namespace Modera\AdminGeneratorBundle\DataMapping;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
interface DataMapperInterface
{
    /**
     * @param array $params
     * @param object $entity
     *
     * @return void
     */
    public function mapData(array $params, $entity);
}