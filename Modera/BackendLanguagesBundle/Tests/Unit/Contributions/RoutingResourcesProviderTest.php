<?php

namespace Modera\BackendLanguagesBundle\Tests\Unit\Contributions;

use Modera\BackendLanguagesBundle\Contributions\RoutingResourcesProvider;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.org>
 */
class RoutingResourcesProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetItems()
    {
        $provider = new RoutingResourcesProvider();

        $items = $provider->getItems();

        $this->assertTrue(is_array($items));
        $this->assertEquals(1, count($items));
    }
}
