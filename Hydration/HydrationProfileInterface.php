<?php

namespace Modera\AdminGeneratorBundle\Hydration;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
interface HydrationProfileInterface
{
    /**
     * @return boolean
     */
    public function isGroupingNeeded();

    /**
     * @return string[]
     */
    public function getGroups();
}