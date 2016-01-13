<?php

namespace Modera\DynamicallyConfigurableMJRBundle\Tests\Unit\Contributions;

use Modera\BackendToolsSettingsBundle\Section\StandardSection;
use Modera\DynamicallyConfigurableMJRBundle\Contributions\SettingsSectionsProvider;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class SettingsSectionsProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetItems()
    {
        $provider = new SettingsSectionsProvider();

        $items = $provider->getItems();

        $this->assertTrue(is_array($items));
        $this->assertEquals(1, count($items));

        /* @var StandardSection $section */
        $section = $items[0];
        $this->assertInstanceOf('Modera\BackendToolsSettingsBundle\Section\StandardSection', $items[0]);
        $this->assertEquals('general', $section->getId());
        $this->assertEquals('General', $section->getName());
        $this->assertEquals('Modera.backend.dcmjr.runtime.GeneralSiteSettingsActivity', $section->getActivityClass());

        $expectedMeta = array(
            'activationParams' => array(
                'category' => 'general',
            ),
        );
        $this->assertEquals($expectedMeta, $section->getMeta());
    }
}
