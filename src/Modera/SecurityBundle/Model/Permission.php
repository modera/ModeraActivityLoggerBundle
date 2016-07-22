<?php

namespace Modera\SecurityBundle\Model;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class Permission implements PermissionInterface
{
    /**
     * @var string
     */
    private $role;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $category;

    /**
     * @var string
     */
    private $description;

    /**
     * @param string $name
     * @param string $role
     * @param string $category
     * @param string $description
     */
    public function __construct($name, $role, $category = null, $description = null)
    {
        $this->category = $category;
        $this->description = $description;
        $this->name = $name;
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }
}
