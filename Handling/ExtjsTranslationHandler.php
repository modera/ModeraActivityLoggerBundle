<?php

namespace Modera\BackendTranslationsToolBundle\Handling;

use Modera\TranslationsBundle\Handling\TemplateTranslationHandler;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ExtjsTranslationHandler extends TemplateTranslationHandler
{
    /**
     * @inheritDoc
     */
    protected function isSourceAvailable($source)
    {
        return 'extjs' == $source;
    }

    /**
     * @inheritDoc
     */
    protected function resolveResourcesDirectory(BundleInterface $bundle)
    {
        return $bundle->getPath() . '/Resources/public/js/';
    }
}