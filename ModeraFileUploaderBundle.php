<?php

namespace Modera\FileUploaderBundle;

use Sli\ExpanderBundle\Ext\ExtensionPoint;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ModeraFileUploaderBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $gatewaysProvider = new ExtensionPoint('modera_file_uploader.uploading.gateways');
        $gatewaysProvider->setDescription('Allows to contribute new uploader gateways.');
        $container->addCompilerPass($gatewaysProvider->createCompilerPass());
    }
}
