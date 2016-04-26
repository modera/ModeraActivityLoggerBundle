<?php

namespace Modera\SecurityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Groups are used to group users.
 *
 * @ORM\Entity(repositoryClass="Modera\SecurityBundle\Entity\GroupRepository")
 * @ORM\Table(name="modera_security_usersgroup", uniqueConstraints={@ORM\UniqueConstraint(name="refName_idx", columns={"refName"})})
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
     * Reference name that maybe used in code to refer exact group.
     * Group with ref.name usually will be created through fixtures.
     *
     * Please note, there is not mandatory Regex assert.
     * But in modera/backend-security-bindle controller this value will
     * be normalized by self::normalizeRefNameString
     *
     * So if plan to use UI editing of your group, try to stick to this Regex assert.
     *
     * @Assert\Regex("/[A-Z_]{0,}/")
     * @ORM\Column(type="string", nullable=true)
     */
    private $refName;

    /**
     * @var Permission[]
     *
     * @ORM\ManyToMany(targetEntity="Permission", mappedBy="groups", cascade={"persist"})
     */
    private $permissions;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->permissions = new ArrayCollection();
    }

    public static function clazz()
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
     * @param Permission $role
     */
    public function addPermission(Permission $role)
    {
        $role->addGroup($this);
        if (!$this->permissions->contains($role)) {
            $this->permissions->add($role);
        }
    }

    /**
     * @param Permission $role
     *
     * @return bool
     */
    public function hasPermission(Permission $role)
    {
        return $this->permissions->contains($role);
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

    public function setPermissions($roles)
    {
        $this->permissions = $roles;
    }

    /**
     * @return Permission[]
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @return mixed
     */
    public function getRefName()
    {
        return $this->refName;
    }

    /**
     * @param mixed $refName
     */
    public function setRefName($refName)
    {
        $this->refName = $refName;
    }

    public static function normalizeRefNameString($proposedRefName)
    {
        $modifiedRefName = strtoupper($proposedRefName);

        return preg_replace('/[^A-Z_]+/', '', $modifiedRefName);
    }
}
