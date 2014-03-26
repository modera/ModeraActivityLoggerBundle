<?php

namespace Modera\SecurityBundle\DataInstallation;

use Doctrine\ORM\EntityManager;
use Modera\SecurityBundle\Entity\Permission;
use Modera\SecurityBundle\Entity\PermissionCategory;
use Modera\SecurityBundle\Model\PermissionCategoryInterface;
use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * Service responsible for installing permissions and permission categories so later they can be used to manager
 * user permissions.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class PermissionAndCategoriesInstaller
{
    private $em;
    private $permissionCategoriesProvider;
    private $permissionsProvider;

    /**
     * @param EntityManager        $em
     * @param ContributorInterface $permissionCategoriesProvider
     * @param ContributorInterface $permissionsProvider
     */
    public function __construct(
        EntityManager $em, ContributorInterface $permissionCategoriesProvider, ContributorInterface $permissionsProvider
    )
    {
        $this->em = $em;
        $this->permissionCategoriesProvider = $permissionCategoriesProvider;
        $this->permissionsProvider = $permissionsProvider;
    }

    /**
     * @return array
     */
    public function installCategories()
    {
        $permissionCategoriesInstalled = 0;

        /* @var PermissionCategoryInterface[] $permissionCategories */
        $permissionCategories = $this->permissionCategoriesProvider->getItems();
        if (count($permissionCategories) > 0) {
            foreach ($permissionCategories as $permissionCategory) {
                /* @var PermissionCategory $entityPermissionCategory */
                $entityPermissionCategory = $this->em->getRepository(PermissionCategory::clazz())->findOneBy(array(
                        'technicalName' => $permissionCategory->getTechnicalName()
                    ));
                if (!$entityPermissionCategory) {
                    $entityPermissionCategory = new PermissionCategory();
                    $entityPermissionCategory->setTechnicalName($permissionCategory->getTechnicalName());

                    $this->em->persist($entityPermissionCategory);

                    $permissionCategoriesInstalled++;
                }

                $entityPermissionCategory->setName($permissionCategory->getName());
            }
        }

        $this->em->flush();

        return array(
            'installed' => $permissionCategoriesInstalled,
            'removed' => 0
        );
    }

    /**
     * @return array
     */
    public function installPermissions()
    {
        $permissionInstalled = 0;

        $permissions = $this->permissionsProvider->getItems();
        foreach ($permissions as $permission) {
            /* @var \Modera\SecurityBundle\Model\PermissionInterface $permission */
            $entityPermission = $this->em->getRepository(Permission::clazz())->findOneBy(array(
                    'roleName' => $permission->getRole()
                ));

            if (!$entityPermission) {
                $entityPermission = new Permission();
                $entityPermission->setRoleName($permission->getRole());

                $this->em->persist($entityPermission);

                $permissionInstalled++;
            }

            $entityPermission->setDescription($permission->getDescription());
            $entityPermission->setName($permission->getName());

            $category = $this->em->getRepository(PermissionCategory::clazz())->findOneBy(array(
                'technicalName' => $permission->getCategory()
            ));
            if ($category) {
                $entityPermission->setCategory($category);
            }
        }

        $this->em->flush();

        return array(
            'installed' => $permissionInstalled,
            'removed' => 0
        );
    }
} 