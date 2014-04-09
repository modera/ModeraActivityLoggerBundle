<?php

namespace Modera\TranslationsBundle\Handling;

use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
interface TranslationHandlerInterface
{
    /**
     * @return string
     */
    public function getBundleName();

    /**
     * @return array
     */
    public function getSources();

    /**
     * Copies translations from file system of a symfony dictionary that eventually
     * will be dumped to database.
     *
     * @param string $source
     * @param string $locale
     * @return MessageCatalogueInterface | null
     */
    public function extract($source, $locale);
}