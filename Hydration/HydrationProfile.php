<?php

namespace Modera\AdminGeneratorBundle\Hydration;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class HydrationProfile implements HydrationProfileInterface
{
    private $isGroupingNeeded;
    private $groups;
    private $extensionPoint;

    /**
     * @inheritDoc
     */
    public function getGroups()
    {
        return $this->groups;
    }

    // fluent interface:

    static public function create($isGroupingNeeded = true)
    {
        $me = new self();
        $me->useGrouping($isGroupingNeeded);

        return $me;
    }

    /**
     * @inheritDoc
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
        $this->isGroupingNeeded = $isGroupingNeeded;

        return $this;
    }
}