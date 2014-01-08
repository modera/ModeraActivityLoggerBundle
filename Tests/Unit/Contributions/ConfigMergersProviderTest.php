<?php

namespace Modera\SecurityAwareJSRuntimeBundle\Tests\Unit\Contributions;

use Modera\JSRuntimeIntegrationBundle\Config\ConfigMergerInterface;
use Modera\SecurityAwareJSRuntimeBundle\Contributions\ConfigMergersProvider;
use Symfony\Component\Security\Core\Role\Role;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ConfigMergersProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testSecurityRolesMerger()
    {
        $roles = array(
            new Role('ROLE_USER'),
            new Role('ROLE_ADMIN')
        );

        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->expects($this->atLeastOnce())
              ->method('getRoles')
              ->will($this->returnValue($roles));

        $sc = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $sc->expects($this->atLeastOnce())
           ->method('getToken')
           ->will($this->returnValue($token));

        $provider = new ConfigMergersProvider($sc);
        $mergers = $provider->getItems();

        $this->assertEquals(1, count($mergers));

        /* @var ConfigMergerInterface $securityRolesMerger */
        $securityRolesMerger = $mergers[0];

        $this->assertInstanceOf('Modera\JSRuntimeIntegrationBundle\Config\ConfigMergerInterface', $securityRolesMerger);

        $existingConfig = array(
            'something' => 'blah'
        );
        $mergedConfig = $securityRolesMerger->merge($existingConfig);

        $this->assertTrue(is_array($mergedConfig));
        $this->assertArrayHasKey('something', $mergedConfig);
        $this->assertEquals('blah', $mergedConfig['something']);
        $this->assertArrayHasKey('userRoles', $mergedConfig);
        $this->assertTrue(is_array($mergedConfig['userRoles']));
        $this->assertEquals(2, count($mergedConfig['userRoles']));
        $this->assertTrue(in_array('ROLE_USER', $mergedConfig['userRoles']));
        $this->assertTrue(in_array('ROLE_ADMIN', $mergedConfig['userRoles']));
    }
}