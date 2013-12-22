<?php

namespace Modera\AdminGeneratorBundle\ExceptionHandling;

use Symfony\Component\HttpKernel\Kernel;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class EnvAwareExceptionHandler implements ExceptionHandlerInterface
{
    private $kernel;

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @inheritDoc
     */
    public function createResponse(\Exception $e, $operation)
    {

    }
}