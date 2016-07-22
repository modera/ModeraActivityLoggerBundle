<?php

namespace Modera\SecurityBundle\Tests\Functional\DependencyInjection;

use Modera\FoundationBundle\Testing\FunctionalTestCase;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ModeraSecurityExtensionTest extends FunctionalTestCase
{
    public function testHowWellHandlerAliasIsEstablished()
    {
        $handler = self::$container->get('modera_security.root_user_handling.handler');

        $this->assertInstanceOf(
            'Modera\SecurityBundle\RootUserHandling\RootUserHandlerInterface',
            $handler
        );
    }
}
