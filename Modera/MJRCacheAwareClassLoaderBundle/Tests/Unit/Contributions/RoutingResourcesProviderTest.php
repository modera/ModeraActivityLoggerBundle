<?php

namespace Modera\MJRCacheAwareClassLoaderBundle\Tests\Unit\Contributions;

use Modera\MJRCacheAwareClassLoaderBundle\Contributions\RoutingResourcesProvider;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class RoutingResourcesProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetItems()
    {
        $provider = new RoutingResourcesProvider();

        $result = $provider->getItems();

        $this->assertTrue(is_array($result));
        $this->assertTrue(in_array('@ModeraMJRCacheAwareClassLoaderBundle/Resources/config/routing.yml', $result));
    }
}
