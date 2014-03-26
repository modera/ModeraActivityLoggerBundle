<?php

namespace Modera\BackendToolsActivityLogBundle\Contributions;

use Modera\BackendToolsBundle\Section\Section;
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
                'Activity log',
                'tools.activitylog',
                'See what activities recently have happened on the site',
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