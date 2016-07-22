<?php

namespace Modera\BackendSecurityBundle\Contributions;

use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ClientDiServiceDefinitionsProvider implements ContributorInterface
{
    /**
     * @return array
     */
    public function getItems()
    {
        return array(
            'modera_backend_security.user.edit_window_contributor' => array(
                'className' => 'Modera.backend.security.toolscontribution.runtime.user.EditWindowContributor',
                'args' => ['@application'],
                'tags' => ['shared_activities_provider'],
            ),
            'modera_backend_security.user.password_window_contributor' => array(
                'className' => 'Modera.backend.security.toolscontribution.runtime.user.PasswordWindowContributor',
                'args' => ['@application'],
                'tags' => ['shared_activities_provider'],
            ),
        );
    }
}
