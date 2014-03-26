<?php

namespace Modera\BackendToolsActivityLogBundle\Contributions;

use Modera\JSRuntimeIntegrationBundle\Sections\Section;
use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class SectionsProvider implements ContributorInterface
{
    private $items;

    public function __construct()
    {
        $this->items = array(
            new Section('tools.activitylog', 'Modera.backend.tools.activitylog.runtime.Section', array(
                Section::META_NAMESPACE => 'Modera.backend.tools.activitylog',
                Section::META_NAMESPACE_PATH => '/bundles/moderabackendtoolsactivitylog/js'
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