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
        var me = this;
        var loadData = function(params, callback) {
            var query = {
                filter: me.getFilter(params),
                hydration: {
                    profile: 'main'
                }
            };
            me.getEndpoint().get(query, callback);
        };

        var createWindow = function(data) {
            var window = Ext.create('Modera.backend.dashboard.view.DashboardSettingsWindow', {
                data: data
            });
            onReadyCallback(window);
        };

        if (Ext.isArray(params.id)) {

            var ids = [];
            var dashboardSettings = null;
            Ext.each(params.id, function(id) {
                loadData({ id: id }, function(response) {
                    if (response.success) {
                        dashboardSettings = response.result['dashboardSettings'];
                        Ext.each(dashboardSettings, function(row) {
                            row['hasAccess'] = false;
                            row['isDefault'] = false;
                        });
                        ids.push(response.result['id']);

                        if (params.id.length == ids.length) {
                            createWindow({
                                id: ids,
                                title: '',
                                dashboardSettings: dashboardSettings
                            });
                        }
                    }
                });
            });
        } else {

            loadData(params, function(response) {
                if (response.success) {
                    createWindow(response.result);
                }
            });
        }
    },

    // private
    attachListeners: function(ui) {
        var me = this;
        ui.on('update', function(w, values) {
            w.disable();

            var records = [];
            if (Ext.isArray(values['id'])) {

                Ext.each(values['id'], function(id) {
                    var data = Ext.clone(values);
                    data['id'] = id;
                    records.push(data);
                });
            } else {

                records.push(values);
            }

            me.getEndpoint().batchUpdate({ records: records }, function(result) {
                if (result.success) {

                    var callback = function () {
                        w.enable();
                        w.close();
                        ModeraFoundation.app.fireEvent('dashboardsettingsupdated');
                    };

                    var configProvider = me.workbench.getService('config_provider');
                    if (configProvider) {
                        var oldConfig = configProvider.cachedConfig;
                        configProvider.cachedConfig = undefined;
                        configProvider.getConfig(function (newConfig) {
                            Ext.applyIf(newConfig, oldConfig);

                            callback();
                        });
                    } else {
                        callback();
                    }
                } else {
                    w.enable();
                }
            })
        });
    }
});