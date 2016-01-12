<?php

namespace Modera\ConfigBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Modera\ConfigBundle\Entity\ConfigurationEntry;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Injects a reference to ConfigurationEntry entities when they are hydrated by Doctrine.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class InitConfigurationEntry
{
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postLoad(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();
        if ($entity instanceof ConfigurationEntry) {
            $entity->init($this->container);
        }
    }
}
