<?php

namespace Modera\BackendToolsSettingsBundle\Tests\Unit\Contributions;

use Modera\BackendToolsSettingsBundle\Contributions\SectionsConfigMerger;
use Modera\BackendToolsSettingsBundle\Section\SectionInterface;
use Sli\ExpanderBundle\Ext\ContributorInterface;

class DummySection implements SectionInterface
{
    public function getId()
    {
        return 'foo-id';
    }

    public function getName()
    {
        return 'foo-id';
    }

    public function getGlyph()
    {
        return 'foo-glyph';
    }

    public function getActivityClass()
    {
        return 'foo-ac';
    }

    public function getMeta()
    {
        return array(
            'megameta'
        );
    }
}

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class SectionsConfigMergerTest extends \PHPUnit_Framework_TestCase
{
    public function testMerge()
    {
        $ds = new DummySection();

        $sectionsProvider = $this->getMock(ContributorInterface::CLAZZ);
        $sectionsProvider->expects($this->atLeastOnce())
                         ->method('getItems')
                         ->will($this->returnValue(array($ds)));

        $configMerger = new SectionsConfigMerger($sectionsProvider);

        $existingConfig = array(
            'someKey' => 'someValue'
        );

        $result = $configMerger->merge($existingConfig);

        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('someKey', $result);
        $this->assertEquals('someValue', $result['someKey']);

        $this->assertArrayHasKey('settingsSections', $result);
        $this->assertTrue(is_array($result['settingsSections']));
        $this->assertEquals(1, count($result['settingsSections']));

        $section = $result['settingsSections'][0];

        $this->assertTrue(is_array($section));
        $this->assertArrayHasKey('id', $section);
        $this->assertEquals($ds->getId(), $section['id']);
        $this->assertArrayHasKey('name', $section);
        $this->assertEquals($ds->getName(), $section['name']);
        $this->assertArrayHasKey('activityClass', $section);
        $this->assertEquals($ds->getActivityClass(), $section['activityClass']);
        $this->assertArrayHasKey('glyph', $section);
        $this->assertEquals($ds->getGlyph(), $section['glyph']);
        $this->assertArrayHasKey('meta', $section);
        $this->assertEquals($ds->getMeta(), $section['meta']);
    }
} 