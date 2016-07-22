<?php

namespace Modera\BackendDashboardBundle\Tests\Contributions;

use Modera\BackendDashboardBundle\Contributions\DashboardProvider;


/**
 * @author    Alex Rudakov <alexandr.rudakov@modera.org>
 * @copyright 2014 Modera Foundation
 */
class DashboardProviderTest extends \PHPUnit_Framework_TestCase {

    public function testItems()
    {
        $provider = new DashboardProvider();

        $items = $provider->getItems();

        foreach($items as $item) {
            $this->assertInstanceOf('Modera\BackendDashboardBundle\Dashboard\DashboardInterface', $item);
        }
    }
} 