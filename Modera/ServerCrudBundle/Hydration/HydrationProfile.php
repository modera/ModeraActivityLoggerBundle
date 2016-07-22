<?php

namespace Modera\ServerCrudBundle\Hydration;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class HydrationProfile implements HydrationProfileInterface
{
    private $isGroupingNeeded;
    private $groups = array();
    private $extensionPoint;

    public static function clazz()
    {
        return get_called_class();
    }

    /**
     * @return string[]
     */
    public function getGroups()
    {
        return $this->groups;
    }

    // fluent interface:

    public static function create($isGroupingNeeded = true)
    {
        $me = new self();
        $me->useGrouping($isGroupingNeeded);

        return $me;
    }

    /**
     * {@inheritdoc}
     */
    public function isGroupingNeeded()
    {
        return $this->isGroupingNeeded;
    }

    public function useGroups(array $groups)
    {
        $this->groups = $groups;

        return $this;
    }

    public function useExtensionPoint($extensionPoint)
    {
        $this->extensionPoint = $extensionPoint;

        return $this;
    }

    public function useGrouping($isGroupingNeeded)
    {
        if (!in_array($isGroupingNeeded, array(true, false), true)) {
            throw new \InvalidArgumentException(
                'Only TRUE or FALSE can be used as a parameter for %s::useGrouping($isGroupingNeeded) method',
                get_class($this)
            );
        }

        $this->isGroupingNeeded = $isGroupingNeeded;

        return $this;
    }
}
