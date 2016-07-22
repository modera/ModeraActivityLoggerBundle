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
    private $category;
    private $serverHandlerConfig;
    private $clientHandlerConfig;
    private $isExposed;
    private $isReadOnly;

    /**
     * @param string $name
     * @param string $readableName
     * @param mixed  $value
     * @param string $category
     * @param array  $serverHandlerConfig
     * @param array  $clientHandlerConfig
     * @param bool   $isReadOnly
     * @param bool   $isExposed
     */
    public function __construct(
        $name, $readableName, $value, $category,
        $serverHandlerConfig = null, $clientHandlerConfig = null,
        $isReadOnly = false, $isExposed = true
    ) {
        $this->name = $name;
        $this->readableName = $readableName;
        $this->value = $value;
        $this->category = $category;
        $this->serverHandlerConfig = $serverHandlerConfig ?: array();
        $this->clientHandlerConfig = $clientHandlerConfig ?: array();
        $this->isReadOnly = $isReadOnly;
        $this->isExposed = $isExposed;
    }

    /**
     * @return bool
     */
    public function isExposed()
    {
        return $this->isExposed;
    }

    /**
     * @return bool
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

    /**
     * @param string $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }
}
