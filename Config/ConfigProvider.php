<?php

namespace Modera\JSRuntimeIntegrationBundle\Config;

/**
 * Service is responsible for providing configuration used by JavaScript runtime.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.net>
 * @copyright 2013 Modera Foundation
 */
class ConfigManager
{
    /**
     * @return array  Config which will be used by client-side js runtime to configure its state
     */
    public function getConfig()
    {
        return array();
    }
}