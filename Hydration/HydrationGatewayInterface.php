<?php

namespace Modera\AdminGeneratorBundle\Hydration;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */ 
interface HydrationGatewayInterface 
{
    /**
     * @return boolean
     */
    public function isGroupingNeeded();
}