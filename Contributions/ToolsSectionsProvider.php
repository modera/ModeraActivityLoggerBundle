<?php

namespace Modera\BackendModuleBundle\Contributions;

use Modera\BackendToolsBundle\Section\Section;
use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * Contributes a section to Backend/Tools
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class ToolsSectionsProvider implements ContributorInterface
{
    private $items;

    public function __construct()
    {
        $this->items = array(
            new Section('Modules', 'tools.modules')
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