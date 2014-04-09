<?php

namespace Modera\BackendSecurityBundle\Controller;

use Modera\BackendSecurityBundle\ModeraBackendSecurityBundle;
use Modera\SecurityBundle\Entity\Group;
use Modera\ServerCrudBundle\Controller\AbstractCrudController;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class GroupsController extends AbstractCrudController
{
    /**
     * @return array
     */
    public function getConfig()
    {
        return array(
            'entity' => Group::clazz(),
            'security' => array(
                'role' => ModeraBackendSecurityBundle::ROLE_ACCESS_BACKEND_TOOLS_SECURITY_SECTION,
                'actions' => array(
                    'create' => ModeraBackendSecurityBundle::ROLE_MANAGE_PERMISSIONS,
                    'update' => ModeraBackendSecurityBundle::ROLE_MANAGE_PERMISSIONS,
                    'remove' => ModeraBackendSecurityBundle::ROLE_MANAGE_PERMISSIONS
                )
            ),
            'hydration' => array(
                'groups' => array(
                    'list' => function(Group $group) {
                            return array(
                                'id' => $group->getId(),
                                'name' => $group->getName(),
                                'usersCount' => count($group->getUsers())
                            );
                        },
                    'delete-group' => ['name'],
                    'main-form' => ['id', 'name'],
                    'compact-list' => ['id', 'name']
                ),
                'profiles' => array(
                    'list', 'compact-list',
                    'delete-group',
                    'edit-group' => array('main-form')
                )
            )
        );
    }
}
