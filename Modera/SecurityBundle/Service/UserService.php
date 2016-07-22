<?php

namespace Modera\SecurityBundle\Service;

use Doctrine\ORM\EntityManager;
use Modera\SecurityBundle\Entity\User;
use Modera\SecurityBundle\Entity\Group;
use Modera\SecurityBundle\Entity\Permission;
use Modera\SecurityBundle\RootUserHandling\RootUserHandlerInterface;
use Modera\FoundationBundle\Translation\T;

/**
 * TODO move logic Doctrine's repository ?
 *
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
     * @throws \RuntimeException If given used is root user and cannot be deleted
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

    /**
     * Find user by some property.
     *
     * @param $property
     * @param $value
     *
     * @return null|User
     */
    public function findUserBy($property, $value)
    {
        return $this->em->getRepository(User::clazz())->findOneBy(array($property => $value));
    }

    /**
     * Find users by some property.
     *
     * @param $property
     * @param $value
     *
     * @return User[]
     */
    public function findUsersBy($property, $value)
    {
        return $this->em->getRepository(User::clazz())->findBy(array($property => $value));
    }

    /**
     * @return User
     */
    public function getRootUser()
    {
        return $this->rootUserHandler->getUser();
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function isRootUser(User $user)
    {
        return $this->rootUserHandler->isRootUser($user);
    }

    /**
     * @param string $roleName
     *
     * @return User[]
     */
    public function getUsersByRole($roleName)
    {
        $ids = $this->getIdsByRole($roleName);
        if (count($ids)) {
            return $this->findUsersBy('id', $ids);
        }

        return array();
    }

    /**
     * @param $roleName
     *
     * @return array
     */
    public function getIdsByRole($roleName)
    {
        $ids = array();

        $qb = $this->em->createQueryBuilder();
        $qb->select('p, u, g')
            ->from(Permission::clazz(), 'p')
            ->leftJoin('p.users', 'u')
            ->leftJoin('p.groups', 'g')
            ->where($qb->expr()->eq('p.roleName', ':roleName'))
            ->setParameter('roleName', $roleName);

        $query = $qb->getQuery();
        $permission = $query->getOneOrNullResult($query::HYDRATE_ARRAY);

        if ($permission) {
            foreach ($permission['users'] as $u) {
                $ids[] = $u['id'];
            }

            $groupIds = array();
            foreach ($permission['groups'] as $g) {
                $groupIds[] = $g['id'];
            }

            if (count($groupIds)) {
                $qb = $this->em->createQueryBuilder();
                $qb->select('g, u')
                    ->from(Group::clazz(), 'g')
                    ->leftJoin('g.users', 'u')
                    ->where($qb->expr()->in('g.id', $groupIds));

                $groups = $qb->getQuery()->getArrayResult();

                foreach ($groups as $g) {
                    foreach ($g['users'] as $u) {
                        $ids[] = $u['id'];
                    }
                }
            }
        }

        return array_keys(array_flip($ids));
    }
}
