<?php

namespace Modera\BackendSecurityBundle\Controller;

use Modera\BackendSecurityBundle\ModeraBackendSecurityBundle;
use Modera\SecurityBundle\Entity\Permission;
use Modera\ServerCrudBundle\Controller\AbstractCrudController;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class PermissionsController extends AbstractCrudController
{
    /**
     * @return array
     */
    public function getConfig()
    {
        return array(
            'entity' => Permission::clazz(),
            'security' => array(
                'role' => ModeraBackendSecurityBundle::ROLE_ACCESS_BACKEND_TOOLS_SECURITY_SECTION,
                'actions' => array(
                    'create' => ModeraBackendSecurityBundle::ROLE_MANAGE_PERMISSIONS,
                    'remove' => ModeraBackendSecurityBundle::ROLE_MANAGE_PERMISSIONS,
                    'update' => ModeraBackendSecurityBundle::ROLE_MANAGE_PERMISSIONS,
                ),
            ),
            'hydration' => array(
                'groups' => array(
                    'list' => function (Permission $permission) {
                        $groups = array();
                        foreach ($permission->getGroups() as $group) {
                            $groups[] = $group->getId();
                        }

                        return array(
                            'id' => $permission->getId(),
                            'name' => $permission->getName(),
                            'category' => $permission->getCategory()->getName(),
                            'groups' => $groups,
                        );
                    },
                ),
                'profiles' => array(
                    'list',
                ),
            ),
        );
    }
}
