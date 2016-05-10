<?php

namespace Modera\SecurityBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Modera\SecurityBundle\RootUserHandling\RootUserHandlerInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Modera\SecurityBundle\Model\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Sli\ExtJsIntegrationBundle\DataMapping\PreferencesAwareUserInterface;

/**
 * @ORM\Table(name="modera_security_user")
 * @ORM\Entity
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 *
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class User implements UserInterface, AdvancedUserInterface, \Serializable, EquatableInterface, PreferencesAwareUserInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     *
     * @Assert\NotBlank
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     *
     * @Assert\NotBlank
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $salt;

    /**
     * @ORM\Column(name="first_name", type="string", nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(name="last_name", type="string", nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(name="middle_name", type="string", nullable=true)
     */
    private $middleName;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $gender;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $state = self::STATE_NEW;

    /**
     * @ORM\ManyToMany(targetEntity="Group", inversedBy="users", cascade={"persist"})
     * @ORM\JoinTable(
     *   name="modera_security_users_groups",
     *   joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    private $groups;

    /**
     * @var RootUserHandlerInterface
     */
    private $rootUserHandler;

    /**
     * @var Permission[]
     *
     * @ORM\ManyToMany(targetEntity="Permission", mappedBy="users", cascade={"persist"})
     */
    private $permissions;

    /**
     * You can use this field to keep meta-information associated with given user. To minimize chance of occurring
     * overlapped keys please store your values under bundle name which owns contributed configuration values. For
     * example, if you have a bundle AcmeFooBundle which wants to save some values to this field then store all values
     * under "acme_foo" key.
     *
     * @ORM\Column(type="json_array")
     */
    private $meta = array();

    public function __construct()
    {
        $this->isActive = true;
        $this->salt = md5(uniqid(null, true));

        $this->groups = new ArrayCollection();
        $this->permissions = new ArrayCollection();
    }

    /**
     * Necessary for providing support for so called "root-users".
     *
     * @see #getRoles() method
     *
     * @private
     *
     * @param RootUserHandlerInterface $rootUserHandler
     */
    public function init(RootUserHandlerInterface $rootUserHandler)
    {
        $this->rootUserHandler = $rootUserHandler;
    }

    /**
     * @param Group $group
     *
     * @return bool
     */
    public function addToGroup(Group $group)
    {
        if (!$group->hasUser($this)) {
            $group->addUser($this);

            return true;
        }

        return false;
    }

    /**
     * @param Permission $role
     */
    public function addPermission(Permission $role)
    {
        $role->addUser($this);
        if (!$this->permissions->contains($role)) {
            $this->permissions[] = $role;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isEqualTo(\Symfony\Component\Security\Core\User\UserInterface $user)
    {
        return $this->username === $user->getUsername();
    }

    /**
     * @return Permission[]
     */
    public function getRawRoles()
    {
        $roles = array();
        foreach ($this->getGroups() as $group) {
            foreach ($group->getPermissions() as $role) {
                $roles[] = $role;
            }
        }
        foreach ($this->permissions as $role) {
            $roles[] = $role;
        }

        return $roles;
    }

    /**
     * This method also takes into account possibility that a user might be "root".
     *
     * @see #init() method.
     * @see \Modera\SecurityBundle\RootUserHandling\RootUserHandlerInterface
     *
     * {@inheritdoc}
     */
    public function getRoles()
    {
        if ($this->rootUserHandler) {
            if ($this->rootUserHandler->isRootUser($this)) {
                return $this->rootUserHandler->getRoles();
            }
        }

        $roles = array('ROLE_USER');
        foreach ($this->getRawRoles() as $role) {
            $roles[] = $role->getRoleName();
        }

        return $roles;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            'id' => $this->id,
            'username' => $this->username,
            'password' => $this->password,
            'salt' => $this->salt,
            'isActive' => $this->isActive,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        foreach (unserialize($serialized) as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * @return string
     */
    public static function clazz()
    {
        return get_called_class();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * {@inheritdoc}
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * @param string $middleName
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;
    }

    /**
     * @param bool $short
     *
     * @return string
     */
    public function getFullName($pattern = 'first last')
    {
        $data = array(
            'first' => $this->getFirstName(),
            'last' => $this->getLastName(),
            'middle' => $this->getMiddleName(),
        );

        $keys = array();
        $values = array();
        foreach ($data as $key => $value) {
            $keys[] = '/\b'.$key.'\b/';
            $values[] = $value;
        }

        return trim(preg_replace($keys, $values, $pattern));
    }

    /**
     * @return string|null
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     */
    public function setGender($gender)
    {
        $gender = strtolower($gender);
        if (!in_array($gender, array(self::GENDER_MALE, self::GENDER_FEMALE))) {
            $gender = null;
        }

        $this->gender = $gender;
    }

    /**
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param int $state
     */
    public function setState($state)
    {
        if (self::STATE_ACTIVE !== $state) {
            $state = self::STATE_NEW;
        }

        $this->state = $state;
    }

    /**
     * @param Group[] $groups
     */
    public function setGroups($groups)
    {
        $this->groups = $groups;
    }

    /**
     * @return Group[]
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @return \Modera\SecurityBundle\Entity\Permission[]
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @return array
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @param array $meta
     */
    public function setMeta(array $meta)
    {
        $this->meta = $meta;
    }

    public function clearMeta()
    {
        $this->meta = array();
    }

    /**
     * {@inheritdoc}
     */
    public function getPreferences()
    {
        return array(
            PreferencesAwareUserInterface::SETTINGS_DATE_FORMAT => 'Y-m-d',
            PreferencesAwareUserInterface::SETTINGS_DATETIME_FORMAT => 'Y-m-d H:i:s',
        );
    }
}
