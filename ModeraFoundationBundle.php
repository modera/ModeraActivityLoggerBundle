<?php

namespace Modera\FoundationBundle;

use Modera\FoundationBundle\Translation\T;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ModeraFoundationBundle extends Bundle
{
    // override
    public function boot()
    {
        $reflClass = new \ReflectionClass(T::clazz());
        $reflProp = $reflClass->getProperty('container');
        $reflProp->setAccessible(true);
        $reflProp->setValue(null, $this->container);
    }
}
