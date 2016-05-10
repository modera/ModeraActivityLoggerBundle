<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        return array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Modera\TranslationsBundle\ModeraTranslationsBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),

            new Sli\ExtJsIntegrationBundle\SliExtJsIntegrationBundle(),
            new Sli\AuxBundle\SliAuxBundle(),
            new Sli\ExpanderBundle\SliExpanderBundle($this),

            new Modera\FoundationBundle\ModeraFoundationBundle(),
            new Modera\MjrIntegrationBundle\ModeraMjrIntegrationBundle(),

            new Modera\DirectBundle\ModeraDirectBundle(),
            new Modera\SecurityBundle\ModeraSecurityBundle(),
            new Modera\BackendToolsBundle\ModeraBackendToolsBundle(),
            new Modera\ActivityLoggerBundle\ModeraActivityLoggerBundle(),

            new Modera\ServerCrudBundle\ModeraServerCrudBundle(),

            new Modera\BackendSecurityBundle\ModeraBackendSecurityBundle(),
        );
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config.yml');
    }

    /**
     * @return string
     */
    public function getCacheDir()
    {
        return sys_get_temp_dir().'/ModeraBackendSecurityBundle/cache';
    }

    /**
     * @return string
     */
    public function getLogDir()
    {
        return sys_get_temp_dir().'/ModeraBackendSecurityBundle/logs';
    }
}
