<?php

namespace Modera\FileRepositoryBundle\Exceptions;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class InvalidRepositoryConfig extends \RuntimeException
{
    private $missingConfigurationKey;
    private $config = array();

    /**
     * @param $missingConfigurationKey
     * @param array $config
     *
     * @return InvalidRepositoryConfig
     */
    public static function create($missingConfigurationKey, array $config)
    {
        $e = new self('This configuration property must be provided: '.$missingConfigurationKey);
        $e->setMissingConfigurationKey($missingConfigurationKey);
        $e->setConfig($config);

        return $e;
    }

    /**
     * @param array $config
     */
    private function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param string $missingConfigurationKey
     */
    private function setMissingConfigurationKey($missingConfigurationKey)
    {
        $this->missingConfigurationKey = $missingConfigurationKey;
    }

    /**
     * @return string
     */
    public function getMissingConfigurationKey()
    {
        return $this->missingConfigurationKey;
    }
}
