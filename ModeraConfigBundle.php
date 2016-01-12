<?php

namespace Modera\ConfigBundle;

use Sli\ExpanderBundle\Ext\ExtensionPoint;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ModeraConfigBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $configEntriesProvider = new ExtensionPoint('modera_config.config_entries');
        $configEntriesProvider->setDescription(
            'Allow to contribute new configuration properties. See ConfigurationEntryInterface.'
        );
        $container->addCompilerPass($configEntriesProvider->createCompilerPass());
    }
}
