<?php

namespace Modera\FileUploaderBundle;

use Sli\ExpanderBundle\DependencyInjection\CompositeContributorsProviderCompilerPass;
use Sli\ExpanderBundle\Ext\ExtensionPoint;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ModeraFileUploaderBundle extends Bundle
{
    /**
     * @inheritDoc
     */
    public function build(ContainerBuilder $container)
    {
        $gatewaysProvider = new ExtensionPoint('modera_file_uploader.uploading.gateways_provider');
        $gatewaysProvider->setDescription('Allows to contribute new uploader gateways.');
        $container->addCompilerPass($gatewaysProvider->createCompilerPass());
    }
}
