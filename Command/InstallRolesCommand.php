<?php

namespace Modera\SecurityBundle\Command;

use Modera\SecurityBundle\Entity\Role;
use Modera\SecurityBundle\Security\PermissionInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\StringInput;
use Modera\SecurityBundle\Entity\User;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class InstallRolesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('modera:security:install-roles')
            ->setDescription('Finds and installs security roles so they can later be associated with users/groups.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var \Sli\ExpanderBundle\Ext\ContributorInterface $permissionProvider */
        $permissionsProvider = $this->getContainer()->get('modera_security.permissions_provider');
        /* @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $permissions = $permissionsProvider->getItems();
        if (count($permissions) > 0) {
            foreach ($permissions as $permission) {
                /* @var PermissionInterface $permission */
                $role = $em->getRepository(Role::clazz())->findOneBy(array(
                        'roleName' => $permission->getRole()
                    ));
                if (!$role) {
                    $role = new Role();
                    $em->persist($role);
                }

                $role->setRoleName($permission->getRole());
                $role->setDescription($permission->getDescription());
                $role->setName($permission->getName());
            }

            $em->flush();

            $output->writeln(' >> Security roles have been successfully installed');
        } else {
            $output->writeln(' >> Nothing to install');
        }
    }
}
