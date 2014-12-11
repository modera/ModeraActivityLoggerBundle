<?php

namespace Modera\RoutingBundle\Tests\Unit\Routing;

use Modera\RoutingBundle\Routing\Loader;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class LoaderTest extends \PHPUnit_Framework_TestCase
{
    /** @var Loader */
    private $loader;

    private $container;
    private $resourcesProvider;
    private $fileLocator;

    /**
     * @param $name
     * @param $path
     * @return RouteCollection
     */
    private function createRouteCollection($name, $path)
    {
        $rc = new RouteCollection();
        $rc->add($name, new Route($path));

        return $rc;
    }

    public function setUp()
    {
        $this->container = \Phake::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->resourcesProvider = \Phake::mock(ContributorInterface::CLAZZ);
        $this->fileLocator = \Phake::mock('Symfony\Component\Config\FileLocatorInterface');

        $this->loader = new Loader($this->container, $this->resourcesProvider, $this->fileLocator);
    }

    public function testLoad()
    {
        $this->assertFalse($this->loader->isLoaded());

        $rootRoutingLoader = \Phake::mock('Symfony\Component\Config\Loader\LoaderInterface');

        \Phake::when($this->resourcesProvider)->getItems()->thenReturn(array('foo-resource'));
        \Phake::when($this->fileLocator)->locate('foo-resource')->thenReturn('foo-resource-body');
        \Phake::when($this->container)->get('routing.loader')->thenReturn($rootRoutingLoader);
        \Phake::when($rootRoutingLoader)->load('foo-resource-body')->thenReturn(
            $this->createRouteCollection('foo', '/article/create')
        );

        /* @var RouteCollection $result */
        $result = $this->loader->load('blah');

        $this->assertInstanceOf('Symfony\Component\Routing\RouteCollection', $result);
        $routes = $result->all();
        $this->assertEquals(1, count($routes));
        $this->assertArrayHasKey('foo', $routes);
        $this->assertSame('/article/create', $routes['foo']->getPath());

        $this->assertTrue($this->loader->isLoaded());

        $thrownException = null;
        try {
            $this->loader->load('blah');
        } catch (\RuntimeException $e) {
            $thrownException = $e;
        }

        $this->assertNotNull($thrownException);
    }

    public function testOrder()
    {
        \Phake::when($this->resourcesProvider)->getItems()->thenReturn(
            array(
                array(
                    'order' => 999,
                    'resource' => 'qux-resource',
                ),
                'bar-resource',
                'baz-resource',
                array(
                    'order' => -999,
                    'resource' => 'foo-resource',
                ),
            )
        );
        \Phake::when($this->fileLocator)->locate('foo-resource')->thenReturn('foo-resource-body');
        \Phake::when($this->fileLocator)->locate('bar-resource')->thenReturn('bar-resource-body');
        \Phake::when($this->fileLocator)->locate('baz-resource')->thenReturn('baz-resource-body');
        \Phake::when($this->fileLocator)->locate('qux-resource')->thenReturn('qux-resource-body');

        $rootRoutingLoader = \Phake::mock('Symfony\Component\Config\Loader\LoaderInterface');
        \Phake::when($this->container)->get('routing.loader')->thenReturn($rootRoutingLoader);

        \Phake::when($rootRoutingLoader)->load('foo-resource-body')->thenReturn(
            $this->createRouteCollection('foo', '/foo')
        );
        \Phake::when($rootRoutingLoader)->load('bar-resource-body')->thenReturn(
            $this->createRouteCollection('bar', '/bar')
        );
        \Phake::when($rootRoutingLoader)->load('baz-resource-body')->thenReturn(
            $this->createRouteCollection('baz', '/baz')
        );
        \Phake::when($rootRoutingLoader)->load('qux-resource-body')->thenReturn(
            $this->createRouteCollection('qux', '/qux')
        );

        $this->assertFalse($this->loader->isLoaded());

        /* @var RouteCollection $result */
        $result = $this->loader->load('blah');

        $routes = $result->all();
        $this->assertEquals(4, count($routes));

        $keys = array_keys($routes);
        $this->assertEquals('foo', $keys[0]);
        $this->assertEquals('bar', $keys[1]);
        $this->assertEquals('baz', $keys[2]);
        $this->assertEquals('qux', $keys[3]);

        $this->assertTrue($this->loader->isLoaded());
    }
} 