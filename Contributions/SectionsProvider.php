<?php

namespace Modera\BackendModuleBundle\Contributions;

use Modera\MjrIntegrationBundle\Sections\Section;
use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class SectionsProvider implements ContributorInterface
{
    private $items;

    public function __construct()
    {
        $this->items = array(
            new Section('tools.modules', 'Modera.backend.module.toolscontribution.runtime.Section', array(
                Section::META_NAMESPACE => 'Modera.backend.module',
                Section::META_NAMESPACE_PATH => '/bundles/moderabackendmodule/js'
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