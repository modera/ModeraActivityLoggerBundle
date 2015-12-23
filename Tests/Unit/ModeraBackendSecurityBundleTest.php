<?php

namespace Modera\BackendSecurityBundle\Tests\Unit;

use Modera\BackendSecurityBundle\DependencyInjection\ServiceAliasCompilerPass;
use Modera\BackendSecurityBundle\ModeraBackendSecurityBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author    Stas Chychkan <stas.chichkan@modera.net>
 * @copyright 2015 Modera Foundation
 */
class ModeraBackendSecurityBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $bundle = new ModeraBackendSecurityBundle();

        $builder = new ContainerBuilder();
        $bundle->build($builder);

        $passes = $builder->getCompiler()->getPassConfig()->getPasses();

        $hasPass = false;
        foreach ($passes as $pass) {
            if ($pass instanceof ServiceAliasCompilerPass) {
                $hasPass = true;
                break;
            }
        }

        $this->assertTrue($hasPass);
    }
}
