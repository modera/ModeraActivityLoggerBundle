<?php

namespace Modera\MJRSecurityIntegrationBundle\Contributions;

use Modera\BackendModuleBundle\ModeraBackendModuleBundle;
use Modera\MJRSecurityIntegrationBundle\ModeraSecurityAwareJSRuntimeBundle;
use Modera\SecurityBundle\Model\Permission;
use Modera\TranslationsBundle\Helper\T;
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
                    T::trans('Access administration interface'),
                    ModeraSecurityAwareJSRuntimeBundle::ROLE_BACKEND_USER,
                    'site'
                )
            ];
        }

        return $this->items;
    }
} 