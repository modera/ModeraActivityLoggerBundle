<?php

namespace Modera\ServerCrudBundle\Security;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class AccessDeniedHttpException extends \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
{
    /**
     * @var string
     */
    private $role;

    /**
     * @param string $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }
}
