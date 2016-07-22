<?php

namespace Modera\DynamicallyConfigurableMJRBundle\Tests\Unit\DependencyInjection;

use Modera\DynamicallyConfigurableMJRBundle\Contributions\ClassLoaderMappingsProvider;
use Modera\DynamicallyConfigurableMJRBundle\Contributions\ConfigEntriesProvider;
use Modera\DynamicallyConfigurableMJRBundle\Contributions\SettingsSectionsProvider;
use Modera\DynamicallyConfigurableMJRBundle\DependencyInjection\ModeraDynamicallyConfigurableMJRExtension;
use Modera\DynamicallyConfigurableMJRBundle\MJR\MainConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class ModeraDynamicallyConfigurableMJRExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $ext = new ModeraDynamicallyConfigurableMJRExtension();

        $builder = new ContainerBuilder();

        $ext->load(array(), $builder);

        $classLoaderMappingsProvider = $builder->getDefinition('modera_dynamically_configurable_mjr.contributions.class_loader_mappings_provider');
        $this->assertEquals(1, count($classLoaderMappingsProvider->getTag('modera_mjr_integration.class_loader_mappings_provider')));
        $this->assertEquals(ClassLoaderMappingsProvider::clazz(), $classLoaderMappingsProvider->getClass());

        $configEntriesProvider = $builder->getDefinition('modera_dynamically_configurable_mjr.contributions.config_entries_provider');
        $this->assertEquals(1, count($configEntriesProvider->getTag('modera_config.config_entries_provider')));
        $this->assertEquals(ConfigEntriesProvider::clazz(), $configEntriesProvider->getClass());

        $settingsSectionsProvider = $builder->getDefinition('modera_dynamically_configurable_mjr.contributions.settings_sections_provider');
        $this->assertEquals(1, count($settingsSectionsProvider->getTag('modera_backend_tools_settings.contributions.sections_provider')));
        $this->assertEquals(SettingsSectionsProvider::clazz(), $settingsSectionsProvider->getClass());

        $mainConfig = $builder->getDefinition('modera_dynamically_configurable_mjr.mjr.main_config');
        $this->assertEquals(MainConfig::clazz(), $mainConfig->getClass());
        /* @var Reference $arg */
        $arg = $mainConfig->getArgument(0);
        $this->assertEquals('modera_config.configuration_entries_manager', (string) $arg);
    }
}
