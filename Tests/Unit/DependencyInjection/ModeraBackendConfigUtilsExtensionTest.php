<?php

namespace Modera\BackendConfigUtilsBundle\Tests\Unit\DependencyInjection;

use Modera\BackendConfigUtilsBundle\DependencyInjection\ModeraBackendConfigUtilsExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class ModeraBackendConfigUtilsExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $ext = new ModeraBackendConfigUtilsExtension();

        $builder = new ContainerBuilder();

        $ext->load(array(), $builder);

        $classLoaderMappingProvider = $builder->getDefinition('modera_backend_config_utils.contributions.class_loader_mappings_provider');
        $this->assertEquals(1, count($classLoaderMappingProvider->getTag('modera_mjr_integration.class_loader_mappings_provider')));
    }
}
