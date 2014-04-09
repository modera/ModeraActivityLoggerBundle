<?php

namespace Modera\TranslationsBundle\Handling;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */ 
class PhpClassesTranslationHandler extends TemplateTranslationHandler
{
    const SOURCE_NAME = 'php-classes';

    /**
     * @inheritDoc
     */
    protected function resolveResourcesDirectory(BundleInterface $bundle)
    {
        return $bundle->getPath();
    }
}