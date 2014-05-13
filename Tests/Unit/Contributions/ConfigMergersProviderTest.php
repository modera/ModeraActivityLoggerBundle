<?php

namespace Modera\MJRSecurityIntegrationBundle\Tests\Unit\Contributions;

use Modera\MjrIntegrationBundle\Config\ConfigMergerInterface;
use Modera\MJRSecurityIntegrationBundle\Contributions\ConfigMergersProvider;
use Sli\ExpanderBundle\Ext\ContributorInterface;
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

        $serviceDefinitions = array(
            'fooService', 'barService'
        );

        $clientDiDefinitionsProvider = $this->getMock(ContributorInterface::CLAZZ);
        $clientDiDefinitionsProvider->expects($this->atLeastOnce())
                                    ->method('getItems')
                                    ->will($this->returnValue($serviceDefinitions));

        $provider = new ConfigMergersProvider($sc, $clientDiDefinitionsProvider);
        $mergers = $provider->getItems();

        $this->assertEquals(2, count($mergers));

        /* @var ConfigMergerInterface $securityRolesMerger */
        $securityRolesMerger = $mergers[0];
        /* @var ConfigMergerInterface $clientDiDefinitionsProviderMerger */
        $clientDiDefinitionsProviderMerger = $mergers[1];

        $this->assertInstanceOf('Modera\MjrIntegrationBundle\Config\ConfigMergerInterface', $securityRolesMerger);
        $this->assertInstanceOf('Modera\MjrIntegrationBundle\Config\ConfigMergerInterface', $clientDiDefinitionsProviderMerger);

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

        $mergedConfig = $clientDiDefinitionsProviderMerger->merge($existingConfig);

        $this->assertArrayHasKey('serviceDefinitions', $mergedConfig);
        $this->assertTrue(is_array($mergedConfig['serviceDefinitions']));
        $this->assertEquals(2, count($mergedConfig['serviceDefinitions']));
        $this->assertTrue(in_array('fooService', $mergedConfig['serviceDefinitions']));
        $this->assertTrue(in_array('barService', $mergedConfig['serviceDefinitions']));
    }
}