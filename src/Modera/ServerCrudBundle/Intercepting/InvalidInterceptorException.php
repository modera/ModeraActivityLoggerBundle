<?php

namespace Modera\ServerCrudBundle\Intercepting;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class InvalidInterceptorException extends \RuntimeException
{
    /**
     * @var object
     */
    private $interceptor;

    /**
     * @param object $interceptor
     *
     * @return InvalidInterceptorException
     */
    public static function create($interceptor)
    {
        $message = sprintf(
            "It is expected that all interceptors would implements %s interface but %s doesn't!",
            '\Modera\ServerCrudBundle\Intercepting\ControllerActionsInterceptorInterface',
            get_class($interceptor)
        );

        $self = new self($message);
        $self->setInterceptor($interceptor);

        return $self;
    }

    /**
     * @param object $interceptor
     */
    public function setInterceptor($interceptor)
    {
        $this->interceptor = $interceptor;
    }

    /**
     * @return object
     */
    public function getInterceptor()
    {
        return $this->interceptor;
    }
}
