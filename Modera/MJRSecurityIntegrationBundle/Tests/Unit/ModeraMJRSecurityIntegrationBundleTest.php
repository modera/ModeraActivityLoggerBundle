<?php

namespace Modera\MJRSecurityIntegrationBundle\Tests\Unit;

use Modera\MJRSecurityIntegrationBundle\ModeraMJRSecurityIntegrationBundle;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2015 Modera Foundation
 */
class ModeraMJRSecurityIntegrationBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $builder = \Phake::mock('Symfony\Component\DependencyInjection\ContainerBuilder');

        $bundle = new ModeraMJRSecurityIntegrationBundle();

        $bundle->build($builder);

        \Phake::verify($builder, \Phake::times(1))
            ->addCompilerPass(
                $this->isInstanceOf('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface')
            )
        ;
    }
}
