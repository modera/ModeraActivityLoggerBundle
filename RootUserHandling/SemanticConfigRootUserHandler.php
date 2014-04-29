<?php

namespace Modera\SecurityBundle\RootUserHandling;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Modera\SecurityBundle\DependencyInjection\ModeraSecurityExtension;
use Modera\SecurityBundle\Entity\Permission;
use Modera\SecurityBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * This implementation will use semantic bundle configuration to retrieve information about root user.
 *
 * @see \Modera\SecurityBundle\DependencyInjection\Configuration
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class SemanticConfigRootUserHandler implements RootUserHandlerInterface
{
    private $config;
    /* @var EntityManager $em */
    private $em;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->config = $container->getParameter(ModeraSecurityExtension::CONFIG_KEY);
        $this->config = $this->config['root_user'];

        $this->em = $container->get('doctrine.orm.entity_manager');
    }

    /**
     * @inheritDoc
     */
    public function isRootUser(User $user)
    {
        /* @var User $rootUser */
        $rootUser = $this->em->getRepository(User::clazz())->findOneBy($this->config['query']);

        if (!$rootUser) {
            throw new RootUserNotFoundException('Unable to find root user using query: ' . json_encode($this->config['query']));
        }

        return $rootUser->isEqualTo($user);
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        $roles = $this->config['roles'];

        if (is_string($roles) && '*' == $roles) {
            $query = sprintf('SELECT e.roleName FROM %s e', Permission::clazz());
            $query = $this->em->createQuery($query);

            $roleNames = array();
            foreach ($query->getResult(Query::HYDRATE_SCALAR) as $roleName) {
                $roleNames[] = $roleName['roleName'];
            }

            return $roleNames;
        } else if (is_array($roles)) {
            return $roles;
        } else {
            throw new \RuntimeException('Neither "*" nor array is used to define root user roles!');
        }
    }
} 