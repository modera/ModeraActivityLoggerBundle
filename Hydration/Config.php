<?php

namespace Modera\ServerCrudBundle\Hydration;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */ 
class Config 
{
    private $rawConfig;

    /**
     * @param array $rawConfig
     */
    public function __construct(array $rawConfig)
    {
        $this->rawConfig = $rawConfig;
    }

    /**
     * @throws UnknownHydrationProfileException
     *
     * @param string $profileName
     *
     * @return mixed
     */
    public function getProfileDefinition($profileName)
    {
        if (!isset($this->rawConfig['profiles'][$profileName])) {
            $e = new UnknownHydrationProfileException(
                "Hydration profile '$profileName' is not found."
            );
            $e->setProfileName($profileName);

            throw $e;
        }

        return $this->rawConfig['profiles'][$profileName];
    }

    public function getGroupDefinition($groupName)
    {
        if (!isset($this->rawConfig['groups'][$groupName])) {
            $e = new UnknownHydrationGroupException(
                "Hydration group '$groupName' is not found."
            );
            $e->setGroupName($groupName);

            throw $e;
        }

        return $this->rawConfig['groups'][$groupName];
    }
}