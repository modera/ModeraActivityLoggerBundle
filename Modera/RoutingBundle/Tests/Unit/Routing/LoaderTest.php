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

    private $resourcesProvider;
    private $rootRoutingLoader;

    /**
     * @param $name
     * @param $path
     *
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
        $this->resourcesProvider = \Phake::mock(ContributorInterface::CLAZZ);
        $this->rootRoutingLoader = \Phake::mock('Symfony\Component\Config\Loader\LoaderInterface');

        $this->loader = new Loader($this->resourcesProvider, $this->rootRoutingLoader);
    }

    public function testLoad()
    {
        $this->assertFalse($this->loader->isLoaded());

        \Phake::when($this->resourcesProvider)->getItems()->thenReturn(array('foo-resource'));
        \Phake::when($this->rootRoutingLoader)->load('foo-resource', null)->thenReturn(
            $this->createRouteCollection('foo', '/article/create')
        );

        /* @var RouteCollection $result */
        $result = $this->loader->load('blah');

        $this->assertInstanceOf('Symfony\Component\Routing\RouteCollection', $result);
        $routes = $result->all();
        $this->assertCount(1, $routes);
        $this->assertArrayHasKey('foo', $routes);
        $this->assertSame('/article/create', $routes['foo']->getPath());

        $this->assertTrue($this->loader->isLoaded());

        $this->setExpectedException('RuntimeException');

        $this->loader->load('blah');
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
                    'resource' => 'ololo-resource',
                    'type' => 'annotation',
                ),
                array(
                    'order' => -999,
                    'resource' => 'foo-resource',
                ),
            )
        );

        \Phake::when($this->rootRoutingLoader)->load('foo-resource', null)->thenReturn(
            $this->createRouteCollection('foo', '/foo')
        );
        \Phake::when($this->rootRoutingLoader)->load('bar-resource', null)->thenReturn(
            $this->createRouteCollection('bar', '/bar')
        );
        \Phake::when($this->rootRoutingLoader)->load('baz-resource', null)->thenReturn(
            $this->createRouteCollection('baz', '/baz')
        );
        \Phake::when($this->rootRoutingLoader)->load('ololo-resource', 'annotation')->thenReturn(
            $this->createRouteCollection('ololo', '/ololo')
        );
        \Phake::when($this->rootRoutingLoader)->load('qux-resource', null)->thenReturn(
            $this->createRouteCollection('qux', '/qux')
        );

        $this->assertFalse($this->loader->isLoaded());

        /* @var RouteCollection $result */
        $result = $this->loader->load('blah');

        $routes = $result->all();
        $this->assertCount(5, $routes);

        $keys = array_keys($routes);
        $this->assertEquals('foo', $keys[0]);
        $this->assertEquals('bar', $keys[1]);
        $this->assertEquals('baz', $keys[2]);
        $this->assertEquals('ololo', $keys[3]);
        $this->assertEquals('qux', $keys[4]);

        $this->assertTrue($this->loader->isLoaded());
    }
}
