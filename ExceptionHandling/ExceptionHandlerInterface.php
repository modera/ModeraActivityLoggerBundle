<?php

namespace Modera\AdminGeneratorBundle\ExceptionHandling;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
interface ExceptionHandlerInterface
{
    /**
     * @param \Exception $e
     * @param string $operation
     *
     * @return array
     */
    public function createResponse(\Exception $e, $operation);
}