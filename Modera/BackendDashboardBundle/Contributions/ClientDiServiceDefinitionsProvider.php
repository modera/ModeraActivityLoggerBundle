<?php

namespace Modera\BackendDashboardBundle\Contributions;

use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author    Alex Rudakov <alexandr.rudakov@modera.org>
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
            'modera_backend_dashboard.user_dashboard_settings_window_contributor' => array(
                'className' => 'Modera.backend.dashboard.runtime.UserDashboardSettingsWindowContributor',
                'args' => ['@application'],
                'tags' => ['shared_activities_provider']
            ),
            'modera_backend_dashboard.settings_window_view_contributor' => array(
                'className' => 'Modera.backend.dashboard.runtime.SettingsWindowContributor',
                'args' => ['@application'],
                'tags' => ['shared_activities_provider']
            )
        );
    }
} 