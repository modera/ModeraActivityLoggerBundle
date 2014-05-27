<?php

namespace Modera\SecurityBundle;

use Sli\ExpanderBundle\DependencyInjection\CompositeContributorsProviderCompilerPass;
use Sli\ExpanderBundle\Ext\ExtensionPoint;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ModeraSecurityBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $permissionsProviders = new ExtensionPoint('modera_security.permissions');
        $permissionsProviders->setDescription(
            'Allows to contribute new permissions that later can be installed by modera:security:install-permissions command.'
        );
        $container->addCompilerPass($permissionsProviders->createCompilerPass());

        $permissionCategoriesProviders = new ExtensionPoint('modera_security.permission_categories');
        $permissionCategoriesProviders->setDescription('Allows to contribute new permission categories.');
        $container->addCompilerPass($permissionCategoriesProviders->createCompilerPass());
    }
}
