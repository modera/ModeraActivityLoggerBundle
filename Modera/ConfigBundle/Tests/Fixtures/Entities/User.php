<?php

namespace Modera\ConfigBundle\Tests\Fixtures\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.org>
 *
 * @ORM\Entity
 */
class User
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $username;

    /**
     * @param string $username
     */
    public function __construct($username = null)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public static function clazz()
    {
        return get_called_class();
    }
}
