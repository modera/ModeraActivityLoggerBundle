<?php

namespace Modera\AdminGeneratorBundle\Hydration;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
interface HydrationGroupInterface
{
    public function isAllowed();

    public function hydrate($entity);
}