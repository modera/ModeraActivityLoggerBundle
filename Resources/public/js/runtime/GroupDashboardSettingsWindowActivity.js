/**
 * @author Alex Rudakov <alexandr.rudakov@modera.org>
 * @copyright 2014 Modera Foundation
 */
Ext.define('Modera.backend.dashboard.runtime.GroupDashboardSettingsWindowActivity', {
    extend: 'Modera.backend.dashboard.runtime.UserDashboardSettingsWindowActivity',

    requires: [
        'Modera.backend.dashboard.view.DashboardSettingsWindow'
    ],

    getEndpoint: function() {
        return Actions.ModeraBackendDashboard_GroupSettings;
    },

    // override
    getId: function() {
        return 'group-dashboard-settings';
    },

    getFilter: function(params) {
        return [
            { property: 'group.id', value: 'eq:' + params.id }
        ]
    }
})