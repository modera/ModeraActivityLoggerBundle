<?php

namespace Modera\TranslationsBundle\Tests\Functional;

use Doctrine\ORM\Tools\SchemaTool;
use Modera\FoundationBundle\Testing\FunctionalTestCase;
use Modera\LanguagesBundle\Entity\Language;
use Modera\TranslationsBundle\Entity\LanguageTranslationToken;
use Modera\TranslationsBundle\Entity\TranslationToken;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class AbstractFunctionalTestCase extends FunctionalTestCase
{
    /**
     * @var SchemaTool
     */
    private static $st;

    // override
    public static function setUpDatabase()
    {
        self::$st = new SchemaTool(self::$em);
        self::$st->createSchema([self::$em->getClassMetadata(Language::clazz())]);
        self::$st->createSchema([self::$em->getClassMetadata(TranslationToken::clazz())]);
        self::$st->createSchema([self::$em->getClassMetadata(LanguageTranslationToken::clazz())]);
    }

    // override
    public static function dropDatabase()
    {
        self::$st->dropSchema([self::$em->getClassMetadata(Language::clazz())]);
        self::$st->dropSchema([self::$em->getClassMetadata(TranslationToken::clazz())]);
        self::$st->dropSchema([self::$em->getClassMetadata(LanguageTranslationToken::clazz())]);
    }

    protected function launchCompileCommand()
    {
        $app = new Application(self::$kernel->getContainer()->get('kernel'));
        $app->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'modera:translations:compile',
        ));
        $input->setInteractive(false);

        $exitCode = $app->run($input, new NullOutput());

        $this->assertEquals(0, $exitCode);
    }

    protected function launchImportCommand()
    {
        $app = new Application(self::$container->get('kernel'));
        $app->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'modera:translations:import',
        ));
        $input->setInteractive(false);

        $exitCode = $app->run($input, new NullOutput());

        $this->assertEquals(0, $exitCode);
    }
}
