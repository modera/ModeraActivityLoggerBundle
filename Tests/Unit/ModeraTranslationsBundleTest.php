<?php

namespace Modera\TranslationsBundle\Tests\Unit;

use Modera\TranslationsBundle\ModeraTranslationsBundle;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ModeraTranslationsBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testTranslationHandlersCompilerPass()
    {
        $containerBuilder = \Phake::mock('Symfony\Component\DependencyInjection\ContainerBuilder');

        $bundle = new ModeraTranslationsBundle;
        $bundle->build($containerBuilder);

        \Phake::verify($containerBuilder)->addCompilerPass(\Phake::capture($pass));

        $this->assertInstanceOf('Modera\TranslationsBundle\DependencyInjection\Compiler\TranslationHandlersCompilerPass', $pass);
    }
}
