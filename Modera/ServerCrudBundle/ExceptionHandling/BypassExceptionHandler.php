<?php

namespace Modera\ServerCrudBundle\ExceptionHandling;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class BypassExceptionHandler implements ExceptionHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function createResponse(\Exception $e, $operation)
    {
        throw $e;
    }
}
