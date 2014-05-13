<?php

namespace Modera\BackendDashboardBundle\Tests\Contributions;

use Modera\BackendDashboardBundle\Contributions\MenuItemsProvider;


/**
 * @author    Alex Rudakov <alexandr.rudakov@modera.org>
 * @copyright 2014 Modera Foundation
 */
class MenuItemsProviderTest extends \PHPUnit_Framework_TestCase {

    public function testItems()
    {
        $provider = new MenuItemsProvider();

        $items = $provider->getItems();

        $this->assertEquals(1, count($items));

        $this->assertInstanceOf('Modera\MjrIntegrationBundle\Menu\MenuItem', $items[0]);
    }

    public function testOrder()
    {
        $provider = new MenuItemsProvider();
        $this->assertTrue(is_integer($provider->getOrder()));
    }
} 