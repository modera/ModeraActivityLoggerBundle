<?php

namespace Modera\MJRSecurityIntegrationBundle\Tests\Unit\DependencyInjection;

use Modera\MJRSecurityIntegrationBundle\DependencyInjection\ModeraMJRSecurityIntegrationExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

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
    }
}
