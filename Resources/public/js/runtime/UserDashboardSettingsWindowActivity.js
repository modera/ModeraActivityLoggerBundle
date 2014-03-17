/**
 * @author Alex Rudakov <alexandr.rudakov@modera.org>
 * @copyright 2014 Modera Foundation
 */
Ext.define('Modera.backend.dashboard.runtime.UserDashboardSettingsWindowActivity', {
    extend: 'MF.activation.activities.AbstractActivity',

    requires: [
        'Modera.backend.dashboard.view.DashboardSettingsWindow'
    ],

    getEndpoint: function() {
        return Actions.ModeraBackendDashboard_UserSettings;
    },

    // override
    getId: function() {
        return 'user-dashboard-settings';
    },

    getFilter: function(params) {
        return [
            { property: 'user.id', value: 'eq:' + params.id }
        ]
    },

    // override
    doCreateUi: function(params, onReadyCallback) {
        var query = {
            filter: this.getFilter(params),
            hydration: {
                profile: 'main'
            }
        };
        this.getEndpoint().get(query, function(response) {
            if (response.success) {
                var window = Ext.create('Modera.backend.dashboard.view.DashboardSettingsWindow', {
                    data: response.result
                });

                onReadyCallback(window);
            }
        });
    },

    // private
    attachListeners: function(ui) {
        var me = this;
        ui.on('update', function(w, data) {
            w.disable();

            me.getEndpoint().update({ record: data }, function(result) {
                w.enable();

                if (result.success) {
                    w.close();
                }
            })
        });
    }
})