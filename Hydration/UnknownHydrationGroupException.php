<?php

namespace Modera\ServerCrudBundle\Hydration;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */ 
class UnknownHydrationGroupException extends \RuntimeException
{
    /**
     * @var string
     */
    private $groupName;

    /**
     * @param string $groupName
     */
    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;
    }

    /**
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupName;
    }
}