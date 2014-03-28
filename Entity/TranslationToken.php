<?php

namespace Modera\TranslationsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="modera_translations_translationtoken")
 *
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class TranslationToken
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
    private $source;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $bundleName;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $domain;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
    private $tokenName;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isObsolete = false;

    /**
     * See {@class \Modera\TranslationsBundle\Listener\LanguageTranslationTokenListener} for details
     *
     * @var array
     * @ORM\Column(type="json_array", nullable=false)
     */
    private $translations = array();

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="LanguageTranslationToken", mappedBy="translationToken", cascade={"persist", "remove"})
     */
    private $languageTranslationTokens;

    public function __construct()
    {
        $this->languageTranslationTokens = new ArrayCollection();
    }

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
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * @return string
     */
    public function getBundleName()
    {
        return $this->bundleName;
    }

    /**
     * @param string $bundleName
     */
    public function setBundleName($bundleName)
    {
        $this->bundleName = $bundleName;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return string
     */
    public function getTokenName()
    {
        return $this->tokenName;
    }

    /**
     * @param string $tokenName
     */
    public function setTokenName($tokenName)
    {
        $this->tokenName = $tokenName;
    }

    /**
     * For ModeraServerCrudBundle
     * @return boolean
     */
    public function getIsObsolete()
    {
        return $this->isObsolete;
    }

    /**
     * @return boolean
     */
    public function isObsolete()
    {
        return $this->isObsolete;
    }

    /**
     * @param boolean $isObsolete
     */
    public function setObsolete($isObsolete)
    {
        $this->isObsolete = $isObsolete;
    }

    /**
     * @return array
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * @param array $translations
     */
    public function setTranslations(array $translations)
    {
        $this->translations = $translations;
    }

    /**
     * @param LanguageTranslationToken $role
     */
    public function addLanguageTranslationToken(LanguageTranslationToken $languageTranslationToken)
    {
        if (!$this->languageTranslationTokens->contains($languageTranslationToken)) {
            $languageTranslationToken->setTranslationToken($this);
            $this->languageTranslationTokens[] = $languageTranslationToken;
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getLanguageTranslationTokens()
    {
        return $this->languageTranslationTokens;
    }

    /**
     * @param ArrayCollection $languageTranslationTokens
     */
    public function setLanguageTranslationTokens(ArrayCollection $languageTranslationTokens)
    {
        $this->languageTranslationTokens = $languageTranslationTokens;
    }
}
