<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        return array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),

            new Sli\AuxBundle\SliAuxBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Sli\ExtJsIntegrationBundle\SliExtJsIntegrationBundle(),

            new Modera\ActivityLoggerBundle\ModeraActivityLoggerBundle()
        );
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config.yml');
    }

    /**
     * @return string
     */
    public function getCacheDir()
    {
        return sys_get_temp_dir().'/ModeraActivityLoggerBundle/cache';
    }

    /**
     * @return string
     */
    public function getLogDir()
    {
        return sys_get_temp_dir().'/ModeraActivityLoggerBundle/logs';
    }
}
