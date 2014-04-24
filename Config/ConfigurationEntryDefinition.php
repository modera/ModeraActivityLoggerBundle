<?php

namespace Modera\ConfigBundle\Config;

/**
 * Use this class to define your configuration properties in config-entries-providers.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ConfigurationEntryDefinition
{
    private $name;
    private $readableName;
    private $value;
    private $serverHandlerConfig;
    private $clientHandlerConfig;
    private $isExposed;
    private $isReadOnly;

    /**
     * @param string $name
     * @param string $readableName
     * @param mixed $value
     * @param array $serverHandlerConfig
     * @param array $clientHandlerConfig
     * @param bool $isReadOnly
     * @param bool $isExposed
     */
    public function __construct(
        $name, $readableName, $value,
        $serverHandlerConfig = null, $clientHandlerConfig = null,
        $isReadOnly = false, $isExposed = true
    )
    {
        $this->name = $name;
        $this->readableName = $readableName;
        $this->value = $value;
        $this->serverHandlerConfig = $serverHandlerConfig ?: array();
        $this->clientHandlerConfig = $clientHandlerConfig ?: array();
        $this->isReadOnly = $isReadOnly;
        $this->isExposed = $isExposed;
    }

    /**
     * @return boolean
     */
    public function issExposed()
    {
        return $this->isExposed;
    }

    /**
     * @return boolean
     */
    public function isReadOnly()
    {
        return $this->isReadOnly;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getReadableName()
    {
        return $this->readableName;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public function getClientHandlerConfig()
    {
        return $this->clientHandlerConfig;
    }

    /**
     * @return array
     */
    public function getServerHandlerConfig()
    {
        return $this->serverHandlerConfig;
    }
} 