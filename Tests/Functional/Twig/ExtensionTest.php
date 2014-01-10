<?php

namespace Modera\FoundationBundle\Tests\Functional\Twig;

use Modera\FoundationBundle\Testing\FunctionalTestCase;
use Modera\FoundationBundle\Twig\Extension;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class ExtensionTest extends FunctionalTestCase
{
    public function testHasExtension()
    {
        /* @var \Twig_Environment $twig */
        $twig = self::$container->get('twig');

        $this->assertTrue($twig->hasExtension(Extension::NAME));
    }

    public function testHasFilters()
    {
        /* @var \Twig_Environment $twig */
        $twig = self::$container->get('twig');

        $this->assertInstanceOf('Twig_SimpleFilter', $twig->getFilter('mf_prepend_every_line'));
    }
}