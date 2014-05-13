<?php

namespace Modera\BackendTranslationsToolBundle\Contributions;

use Modera\MjrIntegrationBundle\Sections\Section;
use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class SectionsProvider implements ContributorInterface
{
    private $items;

    public function __construct()
    {
        $this->items = array(
            new Section('tools.translations', 'Modera.backend.translationstool.toolscontribution.runtime.Section', array(
                Section::META_NAMESPACE => 'Modera.backend.translationstool',
                Section::META_NAMESPACE_PATH => '/bundles/moderabackendtranslationstool/js'
            ))
        );
    }

    /**
     * @inheritDoc
     */
    public function getItems()
    {
        return $this->items;
    }
}