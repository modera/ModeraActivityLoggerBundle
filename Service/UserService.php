<?php

namespace Modera\SecurityBundle\Service;

use Doctrine\ORM\EntityManager;
use Modera\SecurityBundle\Entity\User;
use Modera\SecurityBundle\RootUserHandling\RootUserHandlerInterface;
use Modera\TranslationsBundle\Helper\T;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class UserService
{
    private $em;
    private $rootUserHandler;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em, RootUserHandlerInterface $rootUserHandler)
    {
        $this->em = $em;
        $this->rootUserHandler = $rootUserHandler;
    }

    /**
     * @throws \RuntimeException
     *
     * @param User $user
     */
    public function remove(User $user)
    {
        if ($this->rootUserHandler->isRootUser($user)) {
            throw new \RuntimeException(T::trans('Super admin user never can be deleted.'));
        }

        $this->em->remove($user);
        $this->em->flush();
    }
} 