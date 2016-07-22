<?php

namespace Modera\BackendSecurityBundle\Tests\Unit\Controller;

use Modera\BackendSecurityBundle\Controller\UsersController;

/**
 * Refactored to be a Unit test.
 *
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class UsersControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UsersController
     */
    private $controller;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->controller = new UsersController();
    }

    public function testGeneratePasswordAction()
    {
        $result = $this->controller->generatePasswordAction(array());

        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('result', $result);
        $this->assertTrue(is_array($result['result']));
        $this->assertEquals(1, count($result['result']));
        $this->assertArrayHasKey('plainPassword', $result['result']);
        $this->assertGreaterThan(0, strlen($result['result']['plainPassword']));
    }
}
