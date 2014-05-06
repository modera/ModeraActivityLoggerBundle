<?php

namespace Modera\FileUploaderBundle;

use Sli\ExpanderBundle\DependencyInjection\CompositeContributorsProviderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ModeraFileUploaderBundle extends Bundle
{
    /**
     * @inheritDoc
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(
            new CompositeContributorsProviderCompilerPass('modera_file_uploader.uploading.gateways_provider')
        );
    }
}
