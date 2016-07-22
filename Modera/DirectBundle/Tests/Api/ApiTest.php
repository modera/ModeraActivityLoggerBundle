<?php

namespace Modera\DirectBundle\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Modera\DirectBundle\Api\Api;

class ApiTest extends WebTestCase
{
    /**
     * Test Api->__toString() method.
     */
    public function test__toString()
    {
        $client = $this->createClient();
        $api = new Api($client->getContainer());

        $this->assertRegExp('/Actions/', $api->__toString());
    }
}
