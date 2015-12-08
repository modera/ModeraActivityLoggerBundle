<?php

namespace Modera\ModuleBundle\Tests\Unit\Manipulator;

use Modera\ModuleBundle\Manipulator\KernelManipulator;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DummyBundle extends Bundle
{
    public static function clazz()
    {
        return get_called_class();
    }
}

class AnotherDummyBundle extends DummyBundle
{
}

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2015 Modera Foundation
 */
class KernelManipulatorTest extends \PHPUnit_Framework_TestCase
{
    private $kernelFilename;
    private $appBundlesFilename;

    private $tpl = <<<TEXT
<?php

namespace Modera\ModuleBundle\Tests\Unit\Manipulator;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class DummyModulesAppKernel extends Kernel
{
    public function registerBundles()
    {
        \$bundles = array(
            new \Modera\ModuleBundle\Tests\Unit\Manipulator\DummyBundle(),
        );

        if (in_array(\$this->getEnvironment(), array('dev', 'test'))) {

        }

        return \$bundles;
    }

    public function registerContainerConfiguration(LoaderInterface \$loader)
    {
        \$loader->load(__DIR__ . '/config/config_' . \$this->getEnvironment() . '.yml');
    }
}
TEXT;

    public function setUp()
    {
        $this->kernelFilename = __DIR__.'/DummyModulesAppKernel.php';
        $this->appBundlesFilename = 'AppBundles.php';

        file_put_contents($this->kernelFilename, $this->tpl);
    }

    public function tearDown()
    {
        unlink($this->kernelFilename);
        unlink(__DIR__.DIRECTORY_SEPARATOR.$this->appBundlesFilename);
    }

    public function testAddCode()
    {
        $kernel = new DummyModulesAppKernel('test', true);

        $km = new KernelManipulator($kernel);
        $km->addCode($this->appBundlesFilename);

        // making it possible to load the same class twice by simply renaming it
        $contents = file_get_contents($this->kernelFilename);
        $contents = str_replace('DummyModulesAppKernel', 'PatchedDummyModulesAppKernel', $contents);
        file_put_contents($this->kernelFilename, $contents);
        require $this->kernelFilename;

        file_put_contents(__DIR__.'/AppBundles.php', <<<CODE
<?php return array(
    new \Modera\ModuleBundle\Tests\Unit\Manipulator\AnotherDummyBundle()
);
CODE
);

        $patchedKernel = new PatchedDummyModulesAppKernel('test', true);

        $this->assertTrue(false !== array_search('registerModuleBundles', get_class_methods($patchedKernel)));

        $bundles = $patchedKernel->registerBundles();

        $this->assertTrue(is_array($bundles));
        $this->assertEquals(2, count($bundles));
        $this->assertInstanceOf(DummyBundle::clazz(), $bundles[0]);
        $this->assertInstanceOf(AnotherDummyBundle::clazz(), $bundles[1]);
    }
}
