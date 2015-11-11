<?php

namespace Modera\MjrIntegrationBundle\Tests\Unit\Contributions;

use Modera\MjrIntegrationBundle\AssetsHandling\AssetsProvider;
use Modera\MjrIntegrationBundle\Contributions\ServiceDefinitionsProvider;
use Modera\MjrIntegrationBundle\DependencyInjection\ModeraMjrIntegrationExtension;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2015 Modera Foundation
 */
class ServiceDefinitionsProviderTest extends \PHPUnit_Framework_TestCase
{
    private function createMockContainer(array $jsAssets, array $cssAssets, array $bundleConfig = array())
    {
        $assetsProvider = \Phake::mock(AssetsProvider::clazz());
        \Phake::when($assetsProvider)->getJavascriptAssets(AssetsProvider::TYPE_NON_BLOCKING)->thenReturn($jsAssets);
        \Phake::when($assetsProvider)->getCssAssets(AssetsProvider::TYPE_NON_BLOCKING)->thenReturn($cssAssets);

        $container = \Phake::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        \Phake::when($container)->get('modera_mjr_integration.assets_handling.assets_provider')->thenReturn($assetsProvider);
        \Phake::when($container)->getParameter(ModeraMjrIntegrationExtension::CONFIG_KEY)->thenReturn($bundleConfig);

        return $container;
    }

    public function testGetItemsNoBlockingAssets()
    {
        $container = $this->createMockContainer(array(), array(), array(
            'client_runtime_config_provider_url' => 'foo_url',
        ));

        $provider = new ServiceDefinitionsProvider($container);

        $services = $provider->getItems();

        $this->assertEquals(1, count($services));
        $this->assertArrayHasKey('config_provider', $services);
        $def = $services['config_provider'];
        $this->assertEquals('foo_url', $def['args'][0]['url']);
    }

    public function testGetItemsWithAssets()
    {
        $container = $this->createMockContainer(array('script.js'), array('style.css'), array(
            'client_runtime_config_provider_url' => 'foo_url',
        ));

        $provider = new ServiceDefinitionsProvider($container);

        $services = $provider->getItems();

        $this->assertEquals(3, count($services));
        $this->assertArrayHasKey('non_blocking_assets_loader', $services);
        $loader = $services['non_blocking_assets_loader'];
        $this->assertArrayHasKey('js', $loader['args'][0]);
        $this->assertEquals('script.js', $loader['args'][0]['js'][0]);
        $this->assertArrayHasKey('css', $loader['args'][0]);
        $this->assertEquals('style.css', $loader['args'][0]['css'][0]);
        $this->assertArrayHasKey('non_blocking_assets_workench_loading_blocking_plugin', $services);
    }
}
