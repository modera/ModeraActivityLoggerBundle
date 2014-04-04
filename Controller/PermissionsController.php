<?php

namespace Modera\BackendSecurityBundle\Controller;

use Modera\SecurityBundle\Entity\Permission;
use Modera\ServerCrudBundle\Controller\AbstractCrudController;
use Modera\ServerCrudBundle\Hydration\DoctrineEntityHydrator;
use Modera\ServerCrudBundle\Hydration\HydrationProfile;

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
                'role' => 'ROLE_ACCESS_BACKEND_TOOLS_SECURITY_SECTION'
            ),
            'hydration' => array(
                'groups' => array(
                    'list' => function(Permission $permission) {
                        $groups = array();
                        foreach ($permission->getGroups() as $group) {
                            $groups[] = $group->getId();
                        }

                        return array(
                            'id'       => $permission->getId(),
                            'name'     => $permission->getName(),
                            'category' => $permission->getCategory()->getName(),
                            'groups'   => $groups,
                        );
                    }
                ),
                'profiles' => array(
                    'list',
                )
            )
        );
    }
}
