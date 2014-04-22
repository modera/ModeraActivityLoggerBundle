<?php

namespace Modera\BackendLanguagesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Modera\SecurityBundle\Entity\User;
use Modera\LanguagesBundle\Entity\Language;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 *
 * @ORM\Entity
 * @ORM\Table(name="modera_backendlanguages_usersettings")
 */
class UserSettings
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="Modera\SecurityBundle\Entity\User")
     */
    private $user;

    /**
     * @var Language
     * @ORM\ManyToOne(targetEntity="Modera\LanguagesBundle\Entity\Language")
     */
    private $language;

    static public function clazz()
    {
        return get_called_class();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;
    }

    /**
     * @return Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param Language $language
     */
    public function setLanguage(Language $language = null)
    {
        $this->language = $language;
    }
}
