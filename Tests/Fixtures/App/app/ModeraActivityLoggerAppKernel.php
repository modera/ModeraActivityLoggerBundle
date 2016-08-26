<?php

class ModeraActivityLoggerAppKernel extends \Modera\FoundationBundle\Testing\AbstractFunctionalKernel
{
    public function registerBundles()
    {
        return array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),

            new Sli\AuxBundle\SliAuxBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Sli\ExtJsIntegrationBundle\SliExtJsIntegrationBundle(),

            new Modera\ActivityLoggerBundle\ModeraActivityLoggerBundle(),
        );
    }
}
