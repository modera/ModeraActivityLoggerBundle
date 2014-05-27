<?php

namespace Modera\BackendTranslationsToolBundle;

use Sli\ExpanderBundle\Ext\ExtensionPoint;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Sli\ExpanderBundle\DependencyInjection\CompositeContributorsProviderCompilerPass;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ModeraBackendTranslationsToolBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        $filtersProvider = new ExtensionPoint('modera_backend_translations_tool.filters');
        $filtersProvider->setDescription('Allows to add a new server-side filter (all/new/obsolete filters are implemented using this extension point).');

        $container->addCompilerPass($filtersProvider->createCompilerPass());
    }
}
