<?php

namespace Modera\MJRCacheAwareClassLoaderBundle\Tests\Unit\Contributions;

use Modera\MJRCacheAwareClassLoaderBundle\Contributions\JsResourcesProvider;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class JsResourcesProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetItems()
    {
        $router = \Phake::mock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        \Phake::when($router)->generate('modera_mjr_cache_aware_class_loader')->thenReturn('foo-url');

        $provider = new JsResourcesProvider($router);

        $result = $provider->getItems();

        $this->assertTrue(is_array($result));
        $this->assertTrue(in_array('foo-url', $result));
    }
}
