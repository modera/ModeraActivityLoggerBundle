<?php

namespace Modera\ServerCrudBundle\Hydration;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
interface HydrationGroupInterface
{
    /**
     * @return boolean
     */
    public function isAllowed();

    /**
     * @param object $entity
     * @return array
     */
    public function hydrate($entity);
}