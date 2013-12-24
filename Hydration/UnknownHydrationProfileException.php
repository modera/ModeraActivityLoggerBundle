<?php

namespace Modera\ServerCrudBundle\Hydration;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */ 
class UnknownHydrationProfileException extends \RuntimeException
{
    /**
     * @var string
     */
    private $profileName;

    /**
     * @param string $profileName
     */
    public function setProfileName($profileName)
    {
        $this->profileName = $profileName;
    }

    /**
     * @return string
     */
    public function getProfileName()
    {
        return $this->profileName;
    }
}