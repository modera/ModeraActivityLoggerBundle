<?php

namespace Modera\MjrIntegrationBundle\Config;

use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * Service is responsible for providing configuration used by JavaScript runtime.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.net>
 * @copyright 2013 Modera Foundation
 */
class ConfigManager
{
    private $provider;

    /**
     * @param ContributorInterface $provider
     */
    public function __construct(ContributorInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @return array Config which will be used by client-side js runtime to configure its state
     */
    public function getConfig()
    {
        $result = array();
        foreach ($this->provider->getItems() as $merger) {
            if (!($merger instanceof ConfigMergerInterface)) {
                throw new \RuntimeException();
            }

            $result = $merger->merge($result);
        }

        return $result;
    }
}
