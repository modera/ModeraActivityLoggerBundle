<?php

namespace Modera\SecurityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Groups are used to group users.
 *
 * @ORM\Entity
 * @ORM\Table(name="modera_security_usersgroup")
 *
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class Group
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="groups", cascade={"persist"})
     */
    private $users;

    /**
     * @Assert\NotBlank
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var Role[]
     *
     * @ORM\ManyToMany(targetEntity="Role", mappedBy="groups", cascade={"persist"})
     */
    private $roles;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->roles = new ArrayCollection();
    }

    static public function clazz()
    {
        return get_called_class();
    }

    /**
     * @param User $user
     */
    public function addUser(User $user)
    {
        $this->users->add($user);
        if (!$user->getGroups()->contains($this)) {
            $user->getGroups()->add($this);
        }
    }

    /**
     * @param Role $role
     */
    public function addRole(Role $role)
    {
        $role->addGroup($this);
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }
    }

    /**
     * @param Role $role
     *
     * @return boolean
     */
    public function hasRole(Role $role)
    {
        return $this->roles->contains($role);
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function hasUser(User $user)
    {
        return $this->users->contains($user);
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setUsers($users)
    {
        $this->users = $users;
    }

    /**
     * @return User[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /**
     * @return Role[]
     */
    public function getRoles()
    {
        return $this->roles;
    }
}
