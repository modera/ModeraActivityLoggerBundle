<?php

namespace Modera\ActivityLoggerBundle\Tests\Unit;

use Modera\ActivityLoggerBundle\DependencyInjection\ServiceAliasCompilerPass;
use Modera\ActivityLoggerBundle\ModeraActivityLoggerBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ModeraActivityLoggerBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $bundle = new ModeraActivityLoggerBundle();

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