<?php

namespace Modera\SecurityBundle\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Modera\SecurityBundle\Entity\User;
use Modera\SecurityBundle\RootUserHandling\RootUserHandlerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Doctrine lifecycle listener which injects RootUserHandler instance when User entity is fetched from database.
 *
 * @private
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class RootUserHandlerInjectionListener
{
    /**
     * We have to inject container instead of instance of RootUserHandler service
     * because of when the latter is injected it results in circular dependencies.
     *
     * @var ContainerInterface
     */
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
        /* @var RootUserHandlerInterface $rootUserHandler */
        $rootUserHandler = $this->container->get('modera_security.root_user_handling.handler');

        $entity = $event->getObject();

        if ($entity instanceof User) {
            $entity->init($rootUserHandler);
        }
    }
}
