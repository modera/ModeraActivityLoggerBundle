<?php

namespace Modera\BackendModuleBundle\Contributions;

use Modera\BackendModuleBundle\ModeraBackendModuleBundle;
use Modera\SecurityBundle\Model\Permission;
use Modera\FoundationBundle\Translation\T;
use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class PermissionsProvider implements ContributorInterface
{
    private $items;

    /**
     * @inheritDoc
     */
    public function getItems()
    {
        if (!$this->items) {
            $this->items = [
                new Permission(
                    T::trans('Access modules manager'),
                    ModeraBackendModuleBundle::ROLE_ACCESS_BACKEND_TOOLS_MODULES_SECTION,
                    'site'
                )
            ];
        }

        return $this->items;
    }
} 