<?php

namespace Modera\BackendToolsBundle;

use Sli\ExpanderBundle\DependencyInjection\CompositeContributorsProviderCompilerPass;
use Sli\ExpanderBundle\Ext\ExtensionPoint;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class ModeraBackendToolsBundle extends Bundle
{
    const ROLE_ACCESS_TOOLS_SECTION = 'ROLE_BACKEND_TOOLS_ACCESS_SECTION';

    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        $sectionsProvider = new ExtensionPoint('modera_backend_tools.sections');
        $sectionsProvider->setDescription('Allows to add new sections to Backend/Tools section.');

        $container->addCompilerPass($sectionsProvider->createCompilerPass());
    }
}
