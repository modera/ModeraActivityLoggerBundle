<?php

namespace Modera\BackendSecurityBundle\Contributions;

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
                'Security permissions',
                'tools.security',
                'Control permissions of users/groups.',
                '', '',
                'modera-backend-security-tools-icon'
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