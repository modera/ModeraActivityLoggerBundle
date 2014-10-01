<?php

namespace Modera\SecurityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="modera_security_permission")
 *
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class Permission implements RoleInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Name of symfony security role, something like "ROLE_USER"
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(type="string")
     */
    private $roleName;

    /**
     * A name of this role that can be easily understood by administrator, for instance - "Access admin section"
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="Permission", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="modera_security_rolehierarchy",
     *     joinColumns={@ORM\JoinColumn(name="permission_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="child_id", referencedColumnName="id")}
     * )
     *
     * @var Permission[]
     */
    private $roles;

    /**
     * @var User[]
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="permissions", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="modera_security_permissionusers"
     * )
     */
    private $users;

    /**
     * @var Group[]
     *
     * @ORM\ManyToMany(targetEntity="Group", inversedBy="permissions", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="modera_security_permissiongroups"
     * )
     */
    private $groups;

    /**
     * @var PermissionCategory
     *
     * @Orm\ManyToOne(targetEntity="PermissionCategory", inversedBy="permissions")
     */
    private $category;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->groups = new ArrayCollection();
    }

    static public function clazz()
    {
        return get_called_class();
    }

    public function addUser(User $user)
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }
    }

    public function addGroup(Group $group)
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
        }
    }

    public function addRole(self $role)
    {
        $this->roles[] = $role;
    }

    public function hasGroup(Group $group)
    {
        return $this->groups->contains($group);
    }

    /**
     * @see RoleInterface
     */
    public function getRole()
    {
        return $this->getRoleName();
    }

    // boilerplate:

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setGroups($groups)
    {
        $this->groups = $groups;
    }

    public function getGroups()
    {
        return $this->groups;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setRoleName($roleName)
    {
        $this->roleName = $roleName;
    }

    public function getRoleName()
    {
        return $this->roleName;
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function setUsers($users)
    {
        $this->users = $users;
    }

    public function getUsers()
    {
        return $this->users;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }
}
