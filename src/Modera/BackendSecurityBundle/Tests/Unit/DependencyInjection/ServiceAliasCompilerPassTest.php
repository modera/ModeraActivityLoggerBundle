<?php

namespace Modera\BackendSecurityBundle\Tests\Unit\DependencyInjection;

use Modera\BackendSecurityBundle\DependencyInjection\ModeraBackendSecurityExtension;
use Modera\BackendSecurityBundle\DependencyInjection\ServiceAliasCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author    Stas Chychkan <stas.chichkan@modera.net>
 * @copyright 2015 Modera Foundation
 */
class ServiceAliasCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $builder = new ContainerBuilder();
        $builder->setParameter(ModeraBackendSecurityExtension::CONFIG_KEY, array(
            'mail_service' => 'some_service_id',
        ));

        $this->assertFalse($builder->hasAlias('modera_backend_security.service.mail_service'));

        $cp = new ServiceAliasCompilerPass();
        $cp->process($builder);

        $this->assertEquals('some_service_id', $builder->getAlias('modera_backend_security.service.mail_service'));
    }
}
