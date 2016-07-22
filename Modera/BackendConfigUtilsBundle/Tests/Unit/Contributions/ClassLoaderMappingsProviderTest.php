<?php

namespace Modera\BackendConfigUtilsBundle\Tests\Unit\Contributions;

use Modera\BackendConfigUtilsBundle\Contributions\ClassLoaderMappingsProvider;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class ClassLoaderMappingsProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetItems()
    {
        $provider = new ClassLoaderMappingsProvider();

        $items = $provider->getItems();

        $this->assertTrue(is_array($items));
        $this->assertEquals(1, count($items));
        $this->assertArrayHasKey('Modera.backend.configutils', $items);
        $this->assertEquals('/bundles/moderabackendconfigutils/js', $items['Modera.backend.configutils']);
    }
}
