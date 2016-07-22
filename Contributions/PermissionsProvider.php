<?php

namespace Modera\MJRSecurityIntegrationBundle\Contributions;

use Modera\MJRSecurityIntegrationBundle\ModeraMJRSecurityIntegrationBundle;
use Modera\SecurityBundle\Model\Permission;
use Modera\FoundationBundle\Translation\T;
use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class PermissionsProvider implements ContributorInterface
{
    /**
     * @var Permission[]
     */
    private $items;

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        if (!$this->items) {
            $this->items = [
                new Permission(
                    T::trans('Access administration interface'),
                    ModeraMJRSecurityIntegrationBundle::ROLE_BACKEND_USER,
                    'site'
                ),
            ];
        }

        return $this->items;
    }
}
