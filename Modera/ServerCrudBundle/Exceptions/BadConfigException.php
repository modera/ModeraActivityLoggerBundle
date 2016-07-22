<?php

namespace Modera\ServerCrudBundle\Exceptions;

/**
 * This exception can be thrown when an invalid configuration found during executing.
 *
 * @author    Alex Plaksin <alex.plaksin@modera.org>
 * @copyright 2016 Modera Foundation
 */
class BadConfigException extends \RuntimeException
{
    /**
     * @var string
     */
    protected $serviceType;

    /**
     * @var array
     */
    protected $config;

    /**
     * @return string
     */
    public function getServiceType()
    {
        return $this->serviceType;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param $serviceType
     * @param array      $config
     * @param \Exception $exception
     *
     * @return BadConfigException
     */
    public static function create($serviceType, array $config, \Exception $exception)
    {
        $parentMessage = $exception->getMessage();

        if (array_key_exists($serviceType, $config)) {
            $serviceId = $config[$serviceType];
            $message = sprintf(
                'An error occurred while getting a service for configuration property "%s" using DI service with ID "%s" - %s',
                $serviceType,
                $serviceId,
                $parentMessage
            );
        } else {
            $message = sprintf(
                'An error occurred while getting a configuration property "%s". No such property exists in config.',
                $serviceType
            );
        }

        $generatedException = new self($message, $exception->getCode(), $exception);

        $generatedException->serviceType = $serviceType;
        $generatedException->config = $config;
        $generatedException->exception = $exception;

        return $generatedException;
    }
}
