<?php

namespace Modera\TranslationsBundle\Tests\Functional\EventListener;

use Doctrine\ORM\Tools\SchemaTool;
use Modera\FoundationBundle\Testing\FunctionalTestCase;
use Modera\LanguagesBundle\Entity\Language;
use Modera\TranslationsBundle\Entity\TranslationToken;
use Modera\TranslationsBundle\Entity\LanguageTranslationToken;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class LanguageTranslationTokenListenerTest extends FunctionalTestCase
{
    /**
     * @var SchemaTool
     */
    static private $st;

    // override
    static public function doSetUpBeforeClass()
    {
        self::$st = new SchemaTool(self::$em);
        self::$st->createSchema([self::$em->getClassMetadata(Language::clazz())]);
        self::$st->createSchema([self::$em->getClassMetadata(TranslationToken::clazz())]);
        self::$st->createSchema([self::$em->getClassMetadata(LanguageTranslationToken::clazz())]);

    }

    // override
    static public function doTearDownAfterClass()
    {
        self::$st->dropSchema([self::$em->getClassMetadata(Language::clazz())]);
        self::$st->dropSchema([self::$em->getClassMetadata(TranslationToken::clazz())]);
        self::$st->dropSchema([self::$em->getClassMetadata(LanguageTranslationToken::clazz())]);
    }


    private function createLanguageTranslationToken($locale, $translation, TranslationToken $token)
    {
        $language = new Language;
        $language->setLocale($locale);

        $languageTranslationToken = new LanguageTranslationToken;
        $languageTranslationToken->setLanguage($language);
        $languageTranslationToken->setTranslation($translation);

        $token->addLanguageTranslationToken($languageTranslationToken);

        self::$em->persist($language);
        self::$em->persist($languageTranslationToken);
        self::$em->flush();

        return $languageTranslationToken;
    }

    private function updateLanguageTranslationToken(LanguageTranslationToken $languageTranslationToken)
    {
        $id = $languageTranslationToken->getId();
        $translation = $languageTranslationToken->getTranslation();

        $languageTranslationToken->setTranslation($translation . $id);
        $languageTranslationToken->setNew(false);
        self::$em->persist($languageTranslationToken);
        self::$em->flush();

        return $languageTranslationToken;
    }

    private function compareTranslationsData(TranslationToken $token)
    {
        $this->assertEquals(count($token->getLanguageTranslationTokens()), count($token->getTranslations()));

        /* @var LanguageTranslationToken $ltt */
        $translations = $token->getTranslations();
        foreach ($token->getLanguageTranslationTokens() as $ltt) {
            $this->assertEquals(array(
                'id'          => $ltt->getId(),
                'isNew'       => $ltt->isNew(),
                'translation' => $ltt->getTranslation(),
                'locale'      => $ltt->getLanguage()->getLocale(),
                'language'    => $ltt->getLanguage()->getName(),
            ), $translations[$ltt->getLanguage()->getId()]);
        }
    }

    public function testUpdateTranslationToken()
    {
        $token = new TranslationToken;
        $token->setSource('test');
        $token->setBundleName('test');
        $token->setDomain('test');
        $token->setTokenName('test');
        self::$em->persist($token);
        self::$em->flush();

        $data = array(
            array('en', 'test'),
            array('ru', 'тест'),
        );
        foreach ($data as $trans) {
            $translation = $this->createLanguageTranslationToken($trans[0], $trans[1], $token);
            // postPersist
            $this->compareTranslationsData($token);

            $this->updateLanguageTranslationToken($translation);
            // postUpdate
            $this->compareTranslationsData($token);
        }
    }
}
