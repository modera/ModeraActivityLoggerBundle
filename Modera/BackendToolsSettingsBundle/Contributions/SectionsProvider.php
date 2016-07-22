<?php

namespace Modera\BackendToolsSettingsBundle\Contributions;

use Modera\MjrIntegrationBundle\Sections\Section;
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
            new Section('tools.settings', 'Modera.backend.tools.settings.runtime.Section', array(
                Section::META_NAMESPACE => 'Modera.backend.tools.settings',
                Section::META_NAMESPACE_PATH => '/bundles/moderabackendtoolssettings/js'
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