<?php

namespace Modera\ServerCrudBundle\Tests\Unit\Validation;

use Modera\ServerCrudBundle\Validation\ValidationResult;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class ValidationResultTest extends \PHPUnit_Framework_TestCase
{
    public function testHowWellItWorks()
    {
        $result = new ValidationResult();

        $this->assertFalse($result->hasErrors());

        $result->addFieldError('firstname', 'It is too short');

        $this->assertTrue($result->hasErrors());

        $arrayResult = $result->toArray();

        $this->assertTrue(is_array($arrayResult));
        $this->assertArrayHasKey('field_errors', $arrayResult);

        $this->assertFalse(isset($arrayResult['general_errors']));

        $this->assertTrue(is_array($arrayResult['field_errors']));
        $this->assertEquals(1, count($arrayResult['field_errors']));
        $this->assertArrayHasKey('firstname', $arrayResult['field_errors']);
        $this->assertTrue(is_array($arrayResult['field_errors']['firstname']));
        $this->assertArrayHasKey(0, $arrayResult['field_errors']['firstname']);
        $this->assertEquals('It is too short', $arrayResult['field_errors']['firstname'][0]);

        $firstnameErrors = $result->getFieldErrors('firstname');
        $this->assertTrue(is_array($firstnameErrors));
        $this->assertEquals(1, count($firstnameErrors));
        $this->assertEquals('It is too short', $firstnameErrors[0]);

        $result->addGeneralError('foo error');

        $this->assertTrue(in_array('foo error', $result->getGeneralErrors()));

        $arrayResult = $result->toArray();

        $this->assertEquals(1, count($arrayResult['general_errors']));
        $this->assertTrue(in_array('foo error', $arrayResult['general_errors']));
    }
}