<?php

namespace Modera\BackendSecurityBundle\Tests\Functional\Controller;

use Modera\BackendSecurityBundle\Controller\UsersController;
use Modera\FoundationBundle\Testing\FunctionalTestCase;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class UsersControllerTest extends FunctionalTestCase
{
    /* @var UsersController */
    private $controller;

    // override
    public function doSetUp()
    {
        $this->controller = new UsersController();
        $this->controller->setContainer(self::$container);
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