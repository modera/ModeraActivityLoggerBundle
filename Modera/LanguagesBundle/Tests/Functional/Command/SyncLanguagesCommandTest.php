<?php

namespace Modera\LanguagesBundle\Tests\Functional\Command;

use Doctrine\ORM\Tools\SchemaTool;
use Modera\LanguagesBundle\Entity\Language;
use Modera\FoundationBundle\Testing\FunctionalTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class SyncLanguagesCommandTest extends FunctionalTestCase
{
    /**
     * @var SchemaTool
     */
    static private $st;

    // override
    static public function doSetUpBeforeClass()
    {
        self::$st = new SchemaTool(self::$em);
        self::$st->createSchema(array(self::$em->getClassMetadata(Language::clazz())));

    }

    // override
    static public function doTearDownAfterClass()
    {
        self::$st->dropSchema(array(self::$em->getClassMetadata(Language::clazz())));
    }

    /**
     * @param null|string|array $config
     */
    private function launchCommand($config = null)
    {
        $app = new Application(self::$kernel->getContainer()->get('kernel'));
        $app->setAutoExit(false);
        $input = new ArrayInput(array(
            'command' => 'modera:languages:config-sync-dummy',
            'config'  => $config ? json_encode($config) : null
        ));
        $input->setInteractive(false);

        $result = $app->run($input, new NullOutput());
        $this->assertEquals(0, $result);
    }

    private function checkCount($expected)
    {
        $dbLanguages = self::$em->getRepository(Language::clazz())->findAll();
        $this->assertEquals($expected, count($dbLanguages));
    }

    private function checkEnabled($expected)
    {
        $actual = 0;
        $dbLanguages = self::$em->getRepository(Language::clazz())->findAll();
        foreach ($dbLanguages as $dbLanguage) {
            if ($dbLanguage->getEnabled()) {
                $actual++;
            }
        }
        $this->assertEquals($expected, $actual);
    }

    public function testEmptyConfig()
    {
        $this->checkCount(0);
        $this->launchCommand();
        $this->checkCount(0);
    }

    public function testConfigFile()
    {
        $this->checkCount(0);
        $this->launchCommand('config-file');
        $this->checkCount(3);
        $this->checkEnabled(2);
    }

    public function testSync()
    {
        $this->checkCount(0);

        $this->launchCommand(array(
            array(
                'locale'     => 'en',
                'is_enabled' => true,
            ),
        ));
        $this->checkCount(1);
        $this->checkEnabled(1);

        $this->launchCommand(array(
            array(
                'locale'     => 'en',
                'is_enabled' => false,
            ),
        ));
        $this->checkCount(1);
        $this->checkEnabled(0);

        $this->launchCommand(array(
            array(
                'locale'     => 'en',
                'is_enabled' => true,
            ),
            array(
                'locale'     => 'ru',
                'is_enabled' => true,
            ),
        ));
        $this->checkCount(2);
        $this->checkEnabled(2);

        $this->launchCommand(array(
            array(
                'locale'     => 'en',
                'is_enabled' => false,
            ),
            array(
                'locale'     => 'ru',
                'is_enabled' => true,
            ),
        ));
        $this->checkCount(2);
        $this->checkEnabled(1);

        $this->launchCommand(array(
            array(
                'locale'     => 'en',
                'is_enabled' => true,
            ),
            array(
                'locale'     => 'ru',
                'is_enabled' => false,
            ),
        ));
        $this->checkCount(2);
        $this->checkEnabled(1);

        $this->launchCommand(array(
            array(
                'locale'     => 'ru',
                'is_enabled' => true,
            ),
        ));
        $this->checkCount(2);
        $this->checkEnabled(1);

        $this->launchCommand(array(
            array(
                'locale'     => 'ru',
                'is_enabled' => false,
            ),
        ));
        $this->checkCount(2);
        $this->checkEnabled(0);

        $this->launchCommand(array(
            array(
                'locale'     => 'en',
                'is_enabled' => true,
            ),
            array(
                'locale'     => 'ru',
                'is_enabled' => true,
            ),
        ));
        $this->checkCount(2);
        $this->checkEnabled(2);

        $this->launchCommand();
        $this->checkCount(2);
        $this->checkEnabled(0);
    }
}
