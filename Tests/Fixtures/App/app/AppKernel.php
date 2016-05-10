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
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),

            new Sli\AuxBundle\SliAuxBundle(),
            new Sli\ExtJsIntegrationBundle\SliExtJsIntegrationBundle(),

            new Modera\ServerCrudBundle\ModeraServerCrudBundle(),
            new Modera\ServerCrudBundle\Tests\Fixtures\Bundle\ModeraServerCrudDummyBundle(),
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
        return sys_get_temp_dir().'/ModeraServerCrudBundle/cache';
    }

    /**
     * @return string
     */
    public function getLogDir()
    {
        return sys_get_temp_dir().'/ModeraServerCrudBundle/logs';
    }
}
