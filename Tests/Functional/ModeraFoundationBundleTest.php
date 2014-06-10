<?php

namespace Modera\TranslationsBundle\Tests\Functional;

use Modera\FoundationBundle\Testing\FunctionalTestCase;
use Modera\FoundationBundle\Translation\T;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class ModeraFoundationBundleTest extends FunctionalTestCase
{
    public function testBoot()
    {
        $reflProp = new \ReflectionProperty(T::clazz(), 'container');
        $reflProp->setAccessible(true);

        $this->assertInstanceOf(
            'Symfony\Component\DependencyInjection\ContainerInterface',
            $reflProp->getValue()
        );
    }
}