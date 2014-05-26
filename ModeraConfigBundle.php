<?php

namespace Modera\ConfigBundle;

use Sli\ExpanderBundle\DependencyInjection\CompositeContributorsProviderCompilerPass;
use Sli\ExpanderBundle\Ext\ExtensionPoint;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ModeraConfigBundle extends Bundle
{
    /**
     * @inheritDoc
     */
    public function build(ContainerBuilder $container)
    {
        $configEntriesProvider = new ExtensionPoint('modera_config.config_entries_provider');
        $configEntriesProvider->setDescription(
            'Allow to contribute new configuration properties. See ConfigurationEntryInterface.'
        );
        $container->addCompilerPass($configEntriesProvider->createCompilerPass());
    }
}
