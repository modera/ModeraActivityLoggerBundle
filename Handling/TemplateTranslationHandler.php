<?php

namespace Modera\TranslationsBundle\Handling;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\Extractor\ExtractorInterface;
use Symfony\Bundle\FrameworkBundle\Translation\TranslationLoader;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class TemplateTranslationHandler implements TranslationHandlerInterface
{
    const SOURCE_NAME = 'template';

    /**
     * @var string
     */
    private $bundle;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var ExtractorInterface
     */
    private $extractor;

    /**
     * @var TranslationLoader
     */
    private $loader;

    public function __construct(KernelInterface $kernel, TranslationLoader $loader, ExtractorInterface $extractor, $bundle)
    {
        $this->kernel    = $kernel;
        $this->loader    = $loader;
        $this->extractor = $extractor;
        $this->bundle    = $bundle;
    }

    /**
     * @return string
     */
    public function getBundleName()
    {
        return $this->bundle;
    }

    /**
     * @return array
     */
    public function getSources()
    {
        return array(static::SOURCE_NAME);
    }

    /**
     * @param string $source
     * @param string $locale
     * @return MessageCatalogueInterface | null
     */
    public function extract($source, $locale)
    {
        if (!$this->isSourceAvailable($source)) {
            return null;
        }

        $fs = new Filesystem();

        /* @var Bundle $foundBundle */
        $foundBundle = $this->kernel->getBundle($this->bundle);

        // load any messages from templates
        $extractedCatalogue = new MessageCatalogue($locale);
        $resourcesDir = $this->resolveResourcesDirectory($foundBundle);
        if ($fs->exists($resourcesDir)) {
            $this->extractor->extract($resourcesDir, $extractedCatalogue);
        }

        // load any existing messages from the translation files
        $translationsDir = $foundBundle->getPath() . '/Resources/translations';
        if ($fs->exists($translationsDir)) {
            $currentCatalogue = new MessageCatalogue($locale);
            $this->loader->loadMessages($translationsDir, $currentCatalogue);

            foreach ($extractedCatalogue->getDomains() as $domain) {
                $messages = $currentCatalogue->all($domain);
                if (count($messages)) {
                    $extractedCatalogue->add($messages, $domain);
                }
            }
        }

        return $extractedCatalogue;
    }

    /**
     * @param $source
     * @return bool
     */
    protected function isSourceAvailable($source)
    {
        return static::SOURCE_NAME == $source;
    }

    /**
     * @param Bundle $bundle
     * @return string
     */
    protected function resolveResourcesDirectory(BundleInterface $bundle)
    {
        return $bundle->getPath() . '/Resources/views/';
    }
}