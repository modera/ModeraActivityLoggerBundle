<?php

namespace Modera\SecurityBundle\Model;

/**
 * A higher level of abstraction for Symfony2 security roles, adds some additional information to roles
 * to make them more manageable by non-technical people.
 *
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
interface PermissionInterface
{
    /**
     * @return string|\Symfony\Component\Security\Core\Role\RoleInterface
     */
    public function getRole();

    /**
     * @return string A human understandable name for this permission, for example - Access "Admin" section
     */
    public function getName();

    /**
     * @return string A human understandable description for this permission, for example -
     *                "This permission is used to allow a user see a section in the menu"
     */
    public function getDescription();

    /**
     * @return string
     */
    public function getCategory();
}
