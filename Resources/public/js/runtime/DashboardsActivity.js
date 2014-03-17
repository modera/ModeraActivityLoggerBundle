/**
 * @author Alex Rudakov <alexandr.rudakov@modera.org>
 */
Ext.define('Modera.backend.dashboard.runtime.DashboardsActivity', {
    extend: 'MF.activation.activities.AbstractCompositeActivity',

    /**
     * @private
     * @property {Object[]} dashboards
     */

    // override
    constructor: function() {
        this.callParent(arguments);

        this.dashboards = [];
    },

    // override
    getId: function() {
        return 'home';
    },

    // override
    isHomeActivity: function() {
        return true;
    },

    // override
    doInit: function(callback) {
        var me = this;

        this.workbench.getService('config_provider').getConfig(function(config) {
            me.dashboards = config.modera_backend_dashboard.dashboards;

            callback(me);
        });
    },

    // override
    getZones: function(callback) {
        var me = this;

        if (!this.zones) {
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
            Ext.each(this.dashboards, function(dashboardConfig) {
                zone.activities[dashboardConfig.name] = Ext.create(dashboardConfig.uiClass);
            });

            me.zones = [zone];
        }

        callback(me.zones);
    },

    // override
    doCreateUi: function(params, callback) {
        var ui = Ext.create('Modera.backend.dashboard.view.DashboardPanel', {});
        ui.setDashboards(this.dashboards);

        this.setUpZones(ui);

        callback(ui);
    },

    // override
    getDefaultParams: function() {
        var defaultDashboard = null;
        Ext.each(this.dashboards, function(dashboard) {
            if (dashboard.default) {
                defaultDashboard = dashboard;

                return false;
            }
        });

        if (!defaultDashboard) {
            throw this.$className + '.getDefaultParams(): Unable to find a default dashboard!';
        }

        return { name: defaultDashboard.name };
    }
});