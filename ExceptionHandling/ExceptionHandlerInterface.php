<?php

namespace Modera\ServerCrudBundle\ExceptionHandling;

/**
 * Implementations are responsible for converting exception to a response that will be sent back to client-side.
 *
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
    const OPERATION_GET_NEW_RECORD_VALUES = 'get_new_record_values';

    /**
     * @param \Exception $e
     * @param string     $operation
     *
     * @return array
     */
    public function createResponse(\Exception $e, $operation);
}
