<?php

namespace Modera\AdminGeneratorBundle\Hydration;

/**
 * Service is responsible for converting given entity/entities to something that should be sent back
 * to client-side.
 *
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */ 
class HydrationService 
{
    /**
     * @param array[] $config
     * @param $object
     *
     * @return array
     */
    public function hydrateSingle(array $config, $object)
    {

    }

    /**
     * @param array[] $config
     * @param object[] $objects
     *
     * @return array[]
     */
    public function hydrateBulk(array $config, array $objects)
    {

    }
}