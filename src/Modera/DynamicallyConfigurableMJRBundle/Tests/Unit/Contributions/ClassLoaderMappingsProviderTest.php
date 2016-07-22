<?php

namespace Modera\DynamicallyConfigurableMJRBundle\Tests\Unit\Contributions;

use Modera\DynamicallyConfigurableMJRBundle\Contributions\ClassLoaderMappingsProvider;

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

        $this->assertEquals(1, count($items));
        $this->assertArrayHasKey('Modera.backend.dcmjr', $items);
        $this->assertEquals('/bundles/moderadynamicallyconfigurablemjr/js', $items['Modera.backend.dcmjr']);
    }
}
