<?php

namespace Modera\ActivityLoggerBundle;

use Modera\ActivityLoggerBundle\DependencyInjection\ServiceAliasCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ModeraActivityLoggerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ServiceAliasCompilerPass());
    }
}
