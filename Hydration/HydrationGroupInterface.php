<?php

namespace Modera\AdminGeneratorBundle\Hydration;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */ 
interface HydrationGroupInterface 
{
    public function isAllowed();

    public function hydrate($entity);
}