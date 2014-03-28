<?php

namespace Modera\BackendTranslationsToolBundle\Contributions;

use Modera\BackendToolsBundle\Section\Section;
use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * Contributes a section to Backend/Tools
 *
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ToolsSectionsProvider implements ContributorInterface
{
    private $items;

    public function __construct()
    {
        $this->items = array(
            new Section(
                'Translations',
                'tools.translations',
                'A tool set for translating content from different sources.',
                '', '',
                'modera-backend-translations-tool-tools-icon'
            )
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