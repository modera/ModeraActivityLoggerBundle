<?php

namespace Modera\UpgradeBundle\Tests\Functional\Command;

use Modera\UpgradeBundle\Json\JsonFile;
use Modera\FoundationBundle\Testing\FunctionalTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
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
    private static $basePath;

    /**
     * @var JsonFile
     */
    private static $composerFile;

    /**
     * @var array
     */
    private static $composerBackup;

    /**
     * @var string
     */
    private static $versionFilePath;

    /**
     * {@inheritdoc}
     */
    public static function doSetUpBeforeClass()
    {
        self::$basePath = dirname(self::$kernel->getRootdir());
        self::$composerFile = new JsonFile(self::$basePath.'/composer.json');
        self::$composerBackup = self::$composerFile->read();

        self::$versionFilePath = getcwd().'/modera-version.txt';
        if (file_exists(self::$versionFilePath)) {
            unlink(self::$versionFilePath);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function doTearDownAfterClass()
    {
        self::$composerFile->write(self::$composerBackup);
        unlink(self::$versionFilePath);
    }

    /**
     * @return string
     */
    private function getCurrentVersion()
    {
        return @file_get_contents(self::$versionFilePath);
    }

    /**
     * @param null|string|array $config
     */
    private function getApplication()
    {
        $app = new Application(self::$kernel);
        $app->setAutoExit(false);

        return $app;
    }

    private function runUpdateDependenciesCommand(OutputInterface $output = null)
    {
        $input = new StringInput('modera:upgrade --dependencies '.__DIR__.'/versions.json');
        $input->setInteractive(false);
        $app = $this->getApplication();
        $result = $app->run($input, $output ?: new NullOutput());
        $this->assertEquals(0, $result);
    }

    private function runCommandsCommand(OutputInterface $output = null)
    {
        $input = new StringInput('modera:upgrade --run-commands '.__DIR__.'/versions.json');
        $input->setInteractive(false);
        $app = $this->getApplication();
        $result = $app->run($input, $output ?: new NullOutput());
        $this->assertEquals(0, $result);
    }

    public function testUpgrade()
    {
        $output = new BufferedOutput();

        // null version check
        $expectedData = array(
            'name' => 'modera/upgrade-bundle-test',
            'repositories' => array(
                array(
                    'type' => 'composer',
                    'url' => 'http://packages.org',
                ),
            ),
            'require' => array(
                'test/dependency_1' => 'dev-master',
            ),
        );
        $this->assertEquals($expectedData, self::$composerFile->read());

        // 0.1.0 version check
        $expectedData['require'] = array(
            'test/dependency_1' => '0.1.0',
            'test/dependency_2' => '0.1.0',
            'test/dependency_3' => '0.1.0',
        );
        $expectedData['repositories'][] = array(
            'type' => 'vcs',
            'url' => 'ssh://git@dependency.git',
        );
        $this->runUpdateDependenciesCommand($output);
        $str = $output->fetch();
        $this->assertEquals('0.1.0', $this->getCurrentVersion());
        $this->assertEquals(1, substr_count($str, 'new Test\AddBundle\TestAddBundle()'));
        $this->assertEquals(0, substr_count($str, 'new Test\RemoveBundle\TestRemoveBundle()'));
        $this->assertEquals($expectedData, self::$composerFile->read());
        $this->assertTrue(is_file(self::$basePath.'/composer.backup.json'));
        unlink(self::$basePath.'/composer.backup.json');

        // 0.1.0 version run commands
        $this->runCommandsCommand($output);
        $str = $output->fetch();
        $this->assertEquals(0, substr_count($str, 'help --format=json'));
        $this->assertEquals(0, substr_count($str, 'help --format=txt'));

        // emulate composer.json changes
        $tmp = self::$composerFile->read();
        $tmp['require']['test/dependency_1'] = 'dev-master';
        self::$composerFile->write($tmp);

        // 0.1.1 version check
        $expectedData['require'] = array(
            'test/dependency_1' => '0.1.1',
            'test/dependency_2' => '0.1.0',
        );
        unset($expectedData['repositories'][1]);
        $expectedData['repositories'] = array_values($expectedData['repositories']);
        $this->runUpdateDependenciesCommand($output);
        $str = $output->fetch();
        $this->assertEquals('0.1.1', $this->getCurrentVersion());
        $this->assertEquals(0, substr_count($str, 'new Test\AddBundle\TestAddBundle()'));
        $this->assertEquals(1, substr_count($str, 'new Test\RemoveBundle\TestRemoveBundle()'));
        $this->assertEquals($expectedData, self::$composerFile->read());
        $this->assertTrue(is_file(self::$basePath.'/composer.v0.1.0.backup.json'));
        unlink(self::$basePath.'/composer.v0.1.0.backup.json');

        // 0.1.1 version run commands
        $this->runCommandsCommand($output);
        $str = $output->fetch();
        $this->assertEquals(1, substr_count($str, 'help --format=json'));
        $this->assertEquals(0, substr_count($str, 'help --format=txt'));

        // emulate composer.json changes
        $tmp = self::$composerFile->read();
        $tmp['require']['test/dependency_2'] = 'dev-master';
        self::$composerFile->write($tmp);

        // 0.1.2 version check
        $expectedData['require'] = array(
            'test/dependency_1' => '0.1.1',
            'test/dependency_2' => '0.1.0',
            'test/dependency_4' => '0.1.0',
        );
        $this->runUpdateDependenciesCommand($output);
        $str = $output->fetch();
        $this->assertEquals('0.1.2', $this->getCurrentVersion());
        $this->assertEquals(0, substr_count($str, 'new Test\AddBundle\TestAddBundle()'));
        $this->assertEquals(0, substr_count($str, 'new Test\RemoveBundle\TestRemoveBundle()'));
        $this->assertEquals($expectedData, self::$composerFile->read());
        $this->assertTrue(is_file(self::$basePath.'/composer.v0.1.1.backup.json'));
        unlink(self::$basePath.'/composer.v0.1.1.backup.json');

        // 0.1.2 version run commands
        $this->runCommandsCommand($output);
        $str = $output->fetch();
        $this->assertEquals(0, substr_count($str, 'help --format=json'));
        $this->assertEquals(1, substr_count($str, 'help --format=txt'));

        // 0.1.3 version check
        $this->runUpdateDependenciesCommand($output);
        $str = $output->fetch();
        $this->assertEquals('0.1.3', $this->getCurrentVersion());
        $this->assertEquals(1, substr_count($str, 'Some foo instruction'));
        $this->assertEquals(0, substr_count($str, 'Some bar instruction'));
        $this->assertEquals(0, substr_count($str, 'Some baz instruction'));
        $this->assertEquals($expectedData, self::$composerFile->read());
        $this->assertTrue(is_file(self::$basePath.'/composer.v0.1.2.backup.json'));
        unlink(self::$basePath.'/composer.v0.1.2.backup.json');

        // 0.1.4 version check
        $this->runUpdateDependenciesCommand($output);
        $str = $output->fetch();
        $this->assertEquals('0.1.4', $this->getCurrentVersion());
        $this->assertEquals(0, substr_count($str, 'Some foo instruction'));
        $this->assertEquals(1, substr_count($str, 'Some bar instruction'));
        $this->assertEquals(0, substr_count($str, 'Some baz instruction'));
        $this->assertEquals($expectedData, self::$composerFile->read());
        $this->assertTrue(is_file(self::$basePath.'/composer.v0.1.3.backup.json'));
        unlink(self::$basePath.'/composer.v0.1.3.backup.json');

        // 0.1.5 version check
        $expectedData['require'] = array(
            'test/dependency_1' => '0.1.1',
            'test/dependency_4' => '0.1.1',
        );
        $this->runUpdateDependenciesCommand($output);
        $str = $output->fetch();
        $this->assertEquals('0.1.5', $this->getCurrentVersion());
        $this->assertEquals(0, substr_count($str, 'Some foo instruction'));
        $this->assertEquals(0, substr_count($str, 'Some bar instruction'));
        $this->assertEquals(1, substr_count($str, 'Some baz instruction'));
        $this->assertEquals($expectedData, self::$composerFile->read());
        $this->assertTrue(is_file(self::$basePath.'/composer.v0.1.4.backup.json'));
        unlink(self::$basePath.'/composer.v0.1.4.backup.json');
    }
}
