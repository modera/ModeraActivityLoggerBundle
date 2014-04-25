<?php

namespace Modera\DynamicallyConfigurableAppBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ModeraDynamicallyConfigurableAppBundle extends Bundle
{
    const CONFIG_KERNEL_ENV = 'kernel_env';
    const CONFIG_KERNEL_DEBUG = 'kernel_debug';
}
