/**
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */
Ext.define('Modera.backend.dashboard.runtime.DashboardsView', {
    extend: 'MF.viewsmanagement.views.AbstractCompositeView',

    // override
    getId: function() {
        return 'home';
    },

    // override
    isHomeView: function() {
        return true;
    },

    // override
    getZones: function(callback) {
        var me = this;

        if (!this.zones) {
            this.workbench.getService('config_provider').getConfig(function(config) {
                var dashboardConfig = config.modera_backend_dashboard;
                if (!Ext.isArray(dashboardConfig.dashboards)) {
                    throw 'dat is baddd!';
                }

                var zone = {
                    controllingParam: 'name',
                    activities: {
                    },
                    controller: function(parentUi, zoneUi, dashboardName, callback) {
                        zoneUi.showDashboard(dashboardName);

                        callback();
                    }
                };

                // dynamically populating possible dashboards
                Ext.each(dashboardConfig.dashboards, function(dashboardConfig) {
                    zone.activities[dashboardConfig.name] = Ext.create(dashboardConfig.uiClass);
                });

                me.zones = [zone];

                callback(me.zones);
            });
        } else {
            callback(me.zones);
        }
    },

    // override
    doCreateUi: function(params, callback) {
        var me = this;

        this.workbench.getService('config_provider').getConfig(function(config) {
            var ui = Ext.create('Modera.backend.dashboard.view.DashboardPanel', {});

            var dashboardConfig = config.modera_backend_dashboard;
            if (Ext.isArray(dashboardConfig.dashboards)) {
                ui.setDashboards(dashboardConfig.dashboards);
            }

            me.setUpZones(ui);

            callback(ui);
        });
    }
});