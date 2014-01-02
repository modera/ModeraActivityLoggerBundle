<?php

namespace Modera\ServerCrudBundle\Tests\Functional;

use Doctrine\ORM\Mapping as Orm;

/**
 * @Orm\Entity
 */
class DummyUser
{
    /**
     * @Orm\Column(type="integer")
     * @Orm\Id
     * @Orm\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @Orm\Column(type="string")
     */
    public $firstname;

    /**
     * @Orm\Column(type="string")
     */
    public $lastname;

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    static public function clazz()
    {
        return get_called_class();
    }
}