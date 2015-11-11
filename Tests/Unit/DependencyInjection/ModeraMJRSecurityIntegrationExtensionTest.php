<?php

namespace Modera\MJRSecurityIntegrationBundle\Tests\Unit\DependencyInjection;

use Modera\MJRSecurityIntegrationBundle\DependencyInjection\ModeraMJRSecurityIntegrationExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2015 Modera Foundation
 */
class ModeraMJRSecurityIntegrationExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $semanticConfig = array(
            'modera_mjr_security_integration' => array(
                'login_url' => '/login',
                'logout_url' => '/logout',
                'is_authenticated_url' => '/isAuthenticated',
            ),
        );

        $builder = new ContainerBuilder();

        $ext = new ModeraMJRSecurityIntegrationExtension();
        $ext->load($semanticConfig, $builder);

        $injectedSemanticConfig = $builder->getParameter(ModeraMJRSecurityIntegrationExtension::CONFIG_KEY);
        foreach ($semanticConfig['modera_mjr_security_integration'] as $key => $value) {
            $this->assertArrayHasKey($key, $injectedSemanticConfig);
            $this->assertEquals($value, $injectedSemanticConfig[$key]);
        }

        /* @var Definition $warmerDef */
        $warmerDef = $builder->getDefinition('modera_mjr_security_integration.ext_direct.secured_routing_patch_warmer');
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Definition', $warmerDef);
        $this->assertEquals('Modera\MJRSecurityIntegrationBundle\ExtDirect\SecuredRoutingPatchWarmer', $warmerDef->getClass());
        // it is important that our tag's priority would be 1 because RouterCacheWarmer has priority 0, and we
        // need to run our warmer before, see http://symfony.com/doc/current/reference/dic_tags.html#core-cache-warmers
        $this->assertEquals(array(array('priority' => 1)), $warmerDef->getTag('kernel.cache_warmer'));
    }
}
