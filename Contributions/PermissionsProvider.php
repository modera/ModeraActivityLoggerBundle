<?php

namespace Modera\BackendSecurityBundle\Contributions;

use Modera\BackendSecurityBundle\ModeraBackendSecurityBundle;
use Modera\SecurityBundle\Model\Permission;
use Modera\FoundationBundle\Translation\T;
use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class PermissionsProvider implements ContributorInterface
{
    private $items;

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        if (!$this->items) {
            $this->items = [
                new Permission(
                    T::trans('Access users and groups manager'),
                    ModeraBackendSecurityBundle::ROLE_ACCESS_BACKEND_TOOLS_SECURITY_SECTION,
                    'user-management'
                ),
                new Permission(
                    T::trans('Manage user profiles'),
                    ModeraBackendSecurityBundle::ROLE_MANAGE_USER_PROFILES,
                    'user-management'
                ),
                new Permission(
                    T::trans('Manage permissions'),
                    ModeraBackendSecurityBundle::ROLE_MANAGE_PERMISSIONS,
                    'user-management'
                ),
            ];
        }

        return $this->items;
    }
}
