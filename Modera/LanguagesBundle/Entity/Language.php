<?php

namespace Modera\LanguagesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="modera_languages_language")
 *
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class Language
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $locale;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isEnabled = false;


    static public function clazz()
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
     * @return string
     */
    public function getName($locale = null)
    {
        $enc = 'utf-8';
        $names = Intl::getLocaleBundle()->getLocaleNames($locale ?: $this->locale);
        $str = isset($names[$this->locale]) ? $names[$this->locale] : $this->locale;
        return mb_strtoupper(mb_substr($str, 0, 1, $enc), $enc) . mb_substr($str, 1, mb_strlen($str, $enc), $enc);
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * For ModeraServerCrudBundle
     * @return boolean
     */
    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @param boolean $isEnabled
     */
    public function setEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;
    }
}
