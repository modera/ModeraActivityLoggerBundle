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

        $route = new RouteCollection();
        $route->add('foo', new Route('/article/create'));
        $rootRoutingLoader = \Phake::mock('Symfony\Component\Config\Loader\LoaderInterface');

        \Phake::when($this->resourcesProvider)->getItems()->thenReturn(array('foo-resource'));
        \Phake::when($this->fileLocator)->locate('foo-resource')->thenReturn('foo-resource-body');
        \Phake::when($this->container)->get('routing.loader')->thenReturn($rootRoutingLoader);
        \Phake::when($rootRoutingLoader)->load('foo-resource-body')->thenReturn($route);

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
} 