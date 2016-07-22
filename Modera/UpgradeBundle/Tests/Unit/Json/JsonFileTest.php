<?php

namespace Modera\UpgradeBundle\Tests\Unit\Entity;

use Modera\UpgradeBundle\Json\JsonFile;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class JsonFileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException RuntimeException
     */
    public function testFailureRead()
    {
        $jsonFile = new JsonFile(__DIR__.'/undefined.json');
        $jsonFile->read();
    }

    /**
     * @expectedException RuntimeException
     */
    public function testJsonError()
    {
        $jsonFile = new JsonFile(__DIR__.'/test_error.json');
        $jsonFile->read();
    }

    public function testSuccessfulRead()
    {
        $jsonFile = new JsonFile(__DIR__.'/test_read.json');
        $data = $jsonFile->read();
        $this->assertEquals(array('test' => 'test'), $data);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testFailureWrite()
    {
        $jsonFile = new JsonFile(__DIR__.'/undefined/test2.json');
        $jsonFile->write(array('failure' => 'failure'));
    }

    public function testSuccessfulWrite()
    {
        $jsonFile = new JsonFile(__DIR__.'/test_write.json');
        $data = array('test' => 'test');
        $jsonFile->write($data);
        $this->assertEquals($data, $jsonFile->read());
        unlink(__DIR__.'/test_write.json');
    }
}
