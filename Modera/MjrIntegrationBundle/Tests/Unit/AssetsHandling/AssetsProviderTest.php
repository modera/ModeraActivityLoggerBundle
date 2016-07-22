<?php

namespace Modera\MjrIntegrationBundle\Tests\Unit\AssetsHandling;

use Modera\MjrIntegrationBundle\AssetsHandling\AssetsProvider;
use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2015 Modera Foundation
 */
class AssetsProviderTest extends \PHPUnit_Framework_TestCase
{
    private function createMockProvider($assets)
    {
        $mock = \Phake::mock(ContributorInterface::CLAZZ);
        \Phake::when($mock)->getItems()->thenReturn($assets);

        return $mock;
    }

    private function createIUT(array $cssAssets = array(), array $jsAssets = array())
    {
        $container = \Phake::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        \Phake::when($container)
            ->get('modera_mjr_integration.css_resources_provider')
            ->thenReturn($this->createMockProvider($cssAssets))
        ;
        \Phake::when($container)
            ->get('modera_mjr_integration.js_resources_provider')
            ->thenReturn($this->createMockProvider($jsAssets))
        ;

        return new AssetsProvider($container);
    }

    public function testGetCssAssets()
    {
        $resources = array('blocking.css', '!yo-blocker.css', 'blocking3.css', '*non-blocking.css', 'non*blockingwannabe.css');

        $provider = $this->createIUT($resources);

        $blocking = $provider->getCssAssets(AssetsProvider::TYPE_BLOCKING);

        $this->assertEquals(4, count($blocking));
        $this->assertEquals($resources[0], $blocking[0]);
        $this->assertEquals(substr($resources[1], 1), $blocking[1]);
        $this->assertEquals($resources[4], $blocking[3]);

        $nonBlocking = $provider->getCssAssets(AssetsProvider::TYPE_NON_BLOCKING);

        $this->assertEquals(1, count($nonBlocking));

        $this->assertEquals(substr($resources[3], 1), $nonBlocking[0], 'Returned filename must not contain * suffix');
    }

    public function testGetJsAssets()
    {
        $resources = array('blocking.js', '!yo-blocker.js', 'blocking3.js', '*non-blocking.js', 'non*blockingwannabe.js');

        $provider = $this->createIUT($resources);

        $blocking = $provider->getCssAssets(AssetsProvider::TYPE_BLOCKING);

        $this->assertEquals(4, count($blocking));
        $this->assertEquals($resources[0], $blocking[0]);
        $this->assertEquals(substr($resources[1], 1), $blocking[1]);
        $this->assertEquals($resources[4], $blocking[3]);

        $nonBlocking = $provider->getCssAssets(AssetsProvider::TYPE_NON_BLOCKING);

        $this->assertEquals(1, count($nonBlocking));

        $this->assertEquals(substr($resources[3], 1), $nonBlocking[0], 'Returned filename must not contain * suffix');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetCssAssetsWithInvalidTypeGiven()
    {
        $provider = $this->createIUT(array());

        $provider->getCssAssets('foo');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetJsAssetsWithInvalidTypeGiven()
    {
        $provider = $this->createIUT(array());

        $provider->getJavascriptAssets('foo');
    }
}
