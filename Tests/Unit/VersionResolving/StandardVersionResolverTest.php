<?php

namespace Modera\MJRCacheAwareClassLoaderBundle\Tests\Unit\VersionResolving;

use Modera\MJRCacheAwareClassLoaderBundle\DependencyInjection\ModeraMJRCacheAwareClassLoaderExtension;
use Modera\MJRCacheAwareClassLoaderBundle\VersionResolving\StandardVersionResolver;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class StandardVersionResolverTest extends \PHPUnit_Framework_TestCase
{
    private $container;

    // override
    public function setUp()
    {
        $this->container = \Phake::mock('Symfony\Component\DependencyInjection\ContainerInterface');
    }

    // override
    public function tearDown()
    {
        @unlink(__DIR__.'/../modera-version.txt');
    }

    public function testResolveWithSemanticConfig()
    {
        $config = array(
            'version' => 'foo-bar',
        );

        $kernel = \Phake::mock('Symfony\Component\HttpKernel\KernelInterface');
        \Phake::when($this->container)->get('kernel')->thenReturn($kernel);
        \Phake::when($this->container)->getParameter(ModeraMJRCacheAwareClassLoaderExtension::CONFIG_KEY)->thenReturn($config);

        $resolver = new StandardVersionResolver($this->container);

        $this->assertEquals('foo-bar', $resolver->resolve());
    }

    public function testResolveWithFile()
    {
        file_put_contents(__DIR__.'/../modera-version.txt', 'ololo');

        $kernel = \Phake::mock('Symfony\Component\HttpKernel\KernelInterface');
        \Phake::when($kernel)->getRootDir()->thenReturn(__DIR__);
        \Phake::when($this->container)->get('kernel')->thenReturn($kernel);
        \Phake::when($this->container)->getParameter(ModeraMJRCacheAwareClassLoaderExtension::CONFIG_KEY)->thenReturn(array());

        $resolver = new StandardVersionResolver($this->container);

        $this->assertEquals('ololo', $resolver->resolve());
    }

    public function testResolve()
    {
        $kernel = \Phake::mock('Symfony\Component\HttpKernel\KernelInterface');
        \Phake::when($kernel)->getRootDir()->thenReturn(__DIR__);
        \Phake::when($this->container)->get('kernel')->thenReturn($kernel);
        \Phake::when($this->container)->getParameter(ModeraMJRCacheAwareClassLoaderExtension::CONFIG_KEY)->thenReturn(array());

        $resolver = new StandardVersionResolver($this->container);

        $this->assertEquals('1.0.0', $resolver->resolve());
    }
}
