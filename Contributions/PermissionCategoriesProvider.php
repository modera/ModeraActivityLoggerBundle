<?php

namespace Modera\BackendSecurityBundle\Contributions;

use Modera\SecurityBundle\Model\PermissionCategory;
use Modera\FoundationBundle\Translation\T;
use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class PermissionCategoriesProvider implements ContributorInterface
{
    private $items;

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        if (!$this->items) {
            $this->items = [
                new PermissionCategory(
                    T::trans('User management'),
                    'user-management'
                ),
            ];
        }

        return $this->items;
    }
}
