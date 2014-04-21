<?php

namespace Modera\UpgradeBundle\Tests\Functional\Command;

use Modera\UpgradeBundle\Json\JsonFile;
use Modera\FoundationBundle\Testing\FunctionalTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class UpgradeCommandTest extends FunctionalTestCase
{
    /**
     * @var string
     */
    static private $basePath;

    /**
     * @var JsonFile
     */
    static private $composerFile;

    /**
     * @var array
     */
    static private $composerBackup;

    // override
    static public function doSetUpBeforeClass()
    {
        self::$basePath = dirname(self::$kernel->getContainer()->get('kernel')->getRootdir());
        self::$composerFile = new JsonFile(self::$basePath . '/composer.json');
        self::$composerBackup = self::$composerFile->read();
    }

    // override
    static public function doTearDownAfterClass()
    {
        self::$composerFile->write(self::$composerBackup);
    }

    /**
     * @param null|string|array $config
     */
    private function getApplication()
    {
        $app = new Application(self::$kernel->getContainer()->get('kernel'));
        $app->setAutoExit(false);
        return $app;
    }

    private function runUpdateDependenciesCommand()
    {
        $input = new StringInput('modera:upgrade --dependencies --versions-path=' . __DIR__ . '/versions.json');
        $input->setInteractive(false);
        $app = $this->getApplication();
        $result = $app->run($input, new NullOutput());
        $this->assertEquals(0, $result);
    }

    private function runCommandsCommand()
    {
        $input = new StringInput('modera:upgrade --run-commands');
        $input->setInteractive(false);
        $app = $this->getApplication();
        $result = $app->run($input, new NullOutput());
        $this->assertEquals(0, $result);
    }

    public function testUpgrade()
    {
        $data = array(
            'name'         => 'modera/upgrade-bundle-test',
            'repositories' => array(
                array(
                    'type' => 'composer',
                    'url'  => 'http://packages.org',
                )
            ),
            'require'      => array(
                'test/dependency_1' => 'dev-master',
            ),
        );
        $this->assertEquals($data, self::$composerFile->read());

        $data['version'] = '0.1.0';
        $data['require'] = array(
            'test/dependency_1' => '0.1.0',
            'test/dependency_2' => '0.1.0',
            'test/dependency_3' => '0.1.0',
        );
        $data['repositories'][] = array(
            'type' => 'vcs',
            'url'  => 'ssh://git@dependency.git',
        );
        $this->runUpdateDependenciesCommand();
        $this->assertEquals($data, self::$composerFile->read());
        $this->assertTrue(is_file(self::$basePath . '/composer.backup.json'));
        unlink(self::$basePath . '/composer.backup.json');
        $this->runCommandsCommand();

        $tmp = self::$composerFile->read();
        $tmp['require']['test/dependency_1'] = 'dev-master';
        self::$composerFile->write($tmp);

        $data['version'] = '0.1.1';
        $data['require'] = array(
            'test/dependency_1' => '0.1.1',
            'test/dependency_2' => '0.1.0',
        );
        unset($data['repositories'][1]);
        $data['repositories'] = array_values($data['repositories']);
        $this->runUpdateDependenciesCommand();
        $this->assertEquals($data, self::$composerFile->read());
        $this->assertTrue(is_file(self::$basePath . '/composer.v0.1.0.backup.json'));
        unlink(self::$basePath . '/composer.v0.1.0.backup.json');
        $this->runCommandsCommand();

        $tmp = self::$composerFile->read();
        $tmp['require']['test/dependency_2'] = 'dev-master';
        self::$composerFile->write($tmp);

        $data['version'] = '0.1.2';
        $data['require'] = array(
            'test/dependency_1' => '0.1.1',
            'test/dependency_2' => '0.1.0',
            'test/dependency_4' => '0.1.0',
        );
        $this->runUpdateDependenciesCommand();
        $this->assertEquals($data, self::$composerFile->read());
        $this->assertTrue(is_file(self::$basePath . '/composer.v0.1.1.backup.json'));
        unlink(self::$basePath . '/composer.v0.1.1.backup.json');
        $this->runCommandsCommand();
    }
}
