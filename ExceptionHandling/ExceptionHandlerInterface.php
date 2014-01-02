<?php

namespace Modera\ServerCrudBundle\ExceptionHandling;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
interface ExceptionHandlerInterface
{
    const OPERATION_CREATE = 'create';
    const OPERATION_UPDATE = 'update';
    const OPERATION_REMOVE = 'remove';
    const OPERATION_LIST = 'list';
    const OPERATION_GET = 'get';

    /**
     * @param \Exception $e
     * @param string $operation
     *
     * @return array
     */
    public function createResponse(\Exception $e, $operation);
}