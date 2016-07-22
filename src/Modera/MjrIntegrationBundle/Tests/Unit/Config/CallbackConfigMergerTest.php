<?php

namespace Modera\MjrIntegrationBundle\Tests\Unit\Config;

use Modera\MjrIntegrationBundle\Config\CallbackConfigMerger;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class CallbackConfigMergerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function test__constructWithBadParameter()
    {
        new CallbackConfigMerger('opa');
    }

    public function testHowWellItWorks()
    {
        $merger = new CallbackConfigMerger(function (array $input) {
            return array_merge($input, array(
                'another' => 'value',
            ));
        });

        $result = $merger->merge(array('foo' => 'bar'));

        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('foo', $result);
        $this->assertEquals('bar', $result['foo']);
        $this->assertArrayHasKey('another', $result);
        $this->assertEquals('value', $result['another']);
    }
}
