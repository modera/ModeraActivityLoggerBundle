<?php

namespace Modera\ModuleBundle\Tests\Unit\ModuleHandling;

use Modera\ModuleBundle\ModuleHandling\BundlesResolver;
use Modera\ModuleBundle\ModuleHandling\ModuleBundleInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class MockModuleBundle implements BundleInterface, ModuleBundleInterface
{
    public $bundles = [];
    public $name;

    public function __construct($name = '', array $bundles = [])
    {
        $this->name = $name;
        $this->bundles = $bundles;
    }

    // ModuleBundleInterface:

    public function getBundles(KernelInterface $kernel)
    {
        return $this->bundles;
    }

    // BundleInterface:

    public function getName()
    {
        return $this->name;
    }

    public function boot()
    {
    }

    public function shutdown()
    {
    }

    public function build(ContainerBuilder $container)
    {
    }

    public function getContainerExtension()
    {
    }

    public function getParent()
    {
    }

    public function getNamespace()
    {
    }

    public function getPath()
    {
    }

    public function setContainer(ContainerInterface $container = null)
    {
    }
}

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2015 Modera Foundation
 */
class BundlesResolverTest extends \PHPUnit_Framework_TestCase
{
    private function createMockBundle($name)
    {
        $bundle = \Phake::mock('Symfony\Component\HttpKernel\Bundle\BundleInterface');
        \Phake::when($bundle)->getName()->thenReturn($name);

        return $bundle;
    }

    public function testResolve()
    {
        $kernel = \Phake::mock('Symfony\Component\HttpKernel\KernelInterface');

        $resolver = new BundlesResolver();

        $bundle0 = $this->createMockBundle('bundle-foo');

        $bundle1 = $this->createMockBundle('bundle-bar');
        $bundle2 = $this->createMockBundle('bundle-baz');

        $moduleBundle = new MockModuleBundle('module-foo', [$bundle1, $bundle2]);

        $bundle3 = $this->createMockBundle('bundle-barbaz');

        $anotherModuleBundle = new MockModuleBundle('module-bar', [$moduleBundle, $bundle3, $bundle3]); // one should be ignored

        $bundles = [
            $bundle0, $anotherModuleBundle,
        ];

        $resolvedBundles = $resolver->resolve($bundles, $kernel);

        $this->assertEquals(6, count($resolvedBundles));
        $this->assertSame($bundle0, $resolvedBundles[0]);
        $this->assertSame($bundle1, $resolvedBundles[1]);
        $this->assertSame($bundle2, $resolvedBundles[2]);
        $this->assertSame($moduleBundle, $resolvedBundles[3]);
        $this->assertSame($bundle3, $resolvedBundles[4]);
        $this->assertSame($anotherModuleBundle, $resolvedBundles[5]);
    }
}
