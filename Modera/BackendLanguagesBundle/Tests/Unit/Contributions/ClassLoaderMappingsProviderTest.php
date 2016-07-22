<?php

namespace Modera\BackendLanguagesBundle\Tests\Unit\Contributions;

use Modera\BackendLanguagesBundle\Contributions\ClassLoaderMappingsProvider;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.org>
 */
class ClassLoaderMappingsProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetItems()
    {
        $provider = new ClassLoaderMappingsProvider();

        $items = $provider->getItems();

        $this->assertTrue(is_array($items));
        $this->assertEquals(1, count($items));
        $this->assertArrayHasKey('Modera.backend.languages', $items);
        $this->assertEquals('/bundles/moderabackendlanguages/js', $items['Modera.backend.languages']);
    }
}
