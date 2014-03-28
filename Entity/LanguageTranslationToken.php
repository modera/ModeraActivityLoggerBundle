<?php

namespace Modera\TranslationsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Modera\LanguagesBundle\Entity\Language;

/**
 * @ORM\Entity
 * @ORM\Table(name="modera_translations_languagetranslationtoken")
 *
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class LanguageTranslationToken
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Language
     * @ORM\ManyToOne(targetEntity="Modera\LanguagesBundle\Entity\Language", fetch="EAGER")
     * @ORM\JoinColumn(name="language_id", referencedColumnName="id")
     */
    private $language;

    /**
     * @var TranslationToken
     * @ORM\ManyToOne(targetEntity="TranslationToken", inversedBy="languageTranslationTokens")
     * @ORM\JoinColumn(name="translation_token_id", referencedColumnName="id")
     */
    private $translationToken;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isNew = true;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
    private $translation;


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
     * @return \Modera\LanguagesBundle\Entity\Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param \Modera\LanguagesBundle\Entity\Language $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return \Modera\TranslationsBundle\Entity\TranslationToken
     */
    public function getTranslationToken()
    {
        return $this->translationToken;
    }

    /**
     * @param \Modera\TranslationsBundle\Entity\TranslationToken $translationToken
     */
    public function setTranslationToken($translationToken)
    {
        $this->translationToken = $translationToken;
    }

    /**
     * @return boolean
     */
    public function isNew()
    {
        return $this->isNew;
    }

    /**
     * @param boolean $isNew
     */
    public function setNew($isNew)
    {
        $this->isNew = $isNew;
    }

    /**
     * @return string
     */
    public function getTranslation()
    {
        return $this->translation;
    }

    /**
     * @param string $translation
     */
    public function setTranslation($translation)
    {
        $this->translation = $translation;
    }
}
