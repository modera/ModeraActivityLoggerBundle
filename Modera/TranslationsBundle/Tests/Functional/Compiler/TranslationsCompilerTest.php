<?php

namespace Modera\TranslationsBundle\Tests\Functional\Compiler;

use Modera\TranslationsBundle\Compiler\TranslationsCompiler;
use Modera\TranslationsBundle\Tests\Functional\AbstractFunctionalTestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Smoke test.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class TranslationsCompilerTest extends AbstractFunctionalTestCase
{
    // override
    public static function doSetUpBeforeClass()
    {
        self::setUpDatabase();
    }

    // override
    public static function doTearDownAfterClass()
    {
        self::dropDatabase();
    }

    public function testCompile()
    {
        /* @var KernelInterface $kernel */
        $kernel = self::$container->get('kernel');

        /* @var TranslationsCompiler $compiler */
        $compiler = self::$container->get('modera_translations.compiler.translations_compiler');

        $this->launchImportCommand();

        $compiler->compile();

        $translationsDir = dirname($kernel->getRootdir()).'/app/Resources/translations';

        $discoveredFiles = array();
        foreach (Finder::create()->in($translationsDir) as $file) {
            $discoveredFiles[] = $file->getFilename();
        }

        $this->assertTrue(in_array('ModeraTranslationsDummyBundle', $discoveredFiles));
        $this->assertTrue(in_array('messages.en.yml', $discoveredFiles));
    }
}
