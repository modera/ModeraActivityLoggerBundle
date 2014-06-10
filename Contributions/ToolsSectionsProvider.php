<?php

namespace Modera\BackendToolsSettingsBundle\Contributions;

use Modera\BackendToolsBundle\Section\Section;
use Modera\FoundationBundle\Translation\T;
use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * Contributes a section to Backend/Tools
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ToolsSectionsProvider implements ContributorInterface
{
    private $items;

    public function __construct()
    {
        $this->items = array(
            new Section(
                T::trans('Settings'),
                'tools.settings',
                T::trans('Configure the current site.'),
                '', '',
                'modera-backend-tools-settings-icon'
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