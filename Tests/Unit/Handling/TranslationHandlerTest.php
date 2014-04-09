<?php

namespace Modera\TranslationsBundle\Tests\Unit\Handling;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Bundle\FrameworkBundle\Translation\TranslationLoader;
use Modera\TranslationsBundle\Handling\TemplateTranslationHandler;
use Modera\TranslationsBundle\Handling\PhpClassesTranslationHandler;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class TranslationHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $bundle;

    /**
     * @var \Symfony\Component\HttpKernel\KernelInterface;
     */
    private $kernel;

    /**
     * @var \Symfony\Component\Translation\Extractor\ExtractorInterface
     */
    private $extractor;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Translation\TranslationLoader
     */
    private $loader;

    protected function setUp()
    {
        parent::setUp();

        $this->bundle = 'ModeraTranslationsBundle';
        $this->kernel = \Phake::mock('Symfony\Component\HttpKernel\KernelInterface');
        \Phake::when($this->kernel)->getBundle($this->bundle)->thenReturn(new DummyBundle);
        $this->extractor = \Phake::mock('Symfony\Component\Translation\Extractor\ExtractorInterface');
        $this->loader = new TranslationLoader;
    }

    public function testTemplateTranslationHandler()
    {
        $handler = new TemplateTranslationHandler($this->kernel, $this->loader, $this->extractor, $this->bundle);

        $this->assertInstanceOf('Modera\TranslationsBundle\Handling\TranslationHandlerInterface', $handler);
        $this->assertEquals($this->bundle, $handler->getBundleName());
        $this->assertEquals(array(TemplateTranslationHandler::SOURCE_NAME), $handler->getSources());
        $this->assertNull($handler->extract(PhpClassesTranslationHandler::SOURCE_NAME, 'en'));
        $catalogue = $handler->extract(TemplateTranslationHandler::SOURCE_NAME, 'en');
        $this->assertInstanceOf('Symfony\Component\Translation\MessageCatalogueInterface', $catalogue);
        $this->assertEquals('en', $catalogue->getLocale());
    }

    public function testPhpClassesTranslationHandler()
    {
        $handler = new PhpClassesTranslationHandler($this->kernel, $this->loader, $this->extractor, $this->bundle);

        $this->assertInstanceOf('Modera\TranslationsBundle\Handling\TranslationHandlerInterface', $handler);
        $this->assertEquals($this->bundle, $handler->getBundleName());
        $this->assertEquals(array(PhpClassesTranslationHandler::SOURCE_NAME), $handler->getSources());
        $this->assertNull($handler->extract(TemplateTranslationHandler::SOURCE_NAME, 'ru'));
        $catalogue = $handler->extract(PhpClassesTranslationHandler::SOURCE_NAME, 'ru');
        $this->assertInstanceOf('Symfony\Component\Translation\MessageCatalogueInterface', $catalogue);
        $this->assertEquals('ru', $catalogue->getLocale());
    }
}

class DummyBundle extends Bundle
{
    public function getPath()
    {
        return __DIR__;
    }
}
