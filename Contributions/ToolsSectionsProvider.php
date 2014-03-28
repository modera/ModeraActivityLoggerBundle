<?php

namespace Modera\BackendToolsActivityLogBundle\Contributions;

use Modera\BackendToolsBundle\Section\Section;
use Modera\TranslationsBundle\Helper\T;
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
                T::trans('Activity log'),
                'tools.activitylog',
                T::trans('See what activities recently have happened on the site'),
                '', '',
                'modera-backend-tools-activity-log-icon'
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