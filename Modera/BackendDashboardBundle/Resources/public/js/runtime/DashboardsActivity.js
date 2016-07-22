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

        this.attachListeners();

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
        this.loadConfig(callback);
    },

    loadConfig: function (callback) {
        var me = this;

        this.workbench.getService('config_provider').getConfig(function(config) {
            me.dashboards = config.modera_backend_dashboard.dashboards;

            callback(me);
        });
    },


    // internal
    onSectionLoaded: function(section) {
        section.relayEvents(this.getUi(), ['handleaction']);
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
    },

    attachListeners: function () {
        var me = this;

        this.dashboardUpdateListener = ModeraFoundation.app.on('dashboardsettingsupdated', this.reloadActivity, me, {destroyable: true});
        me.on('deactivated', this.detachListeners, me);
    },

    detachListeners: function () {
        this.dashboardUpdateListener.destroy();
    },

    reloadActivity: function () {
        var me = this;

        var activityManager = me.workbench.getActivitiesManager(),
            isHomeActivity = activityManager.hasActivity('home');

        if (isHomeActivity) {
            me.workbench.activateSection('dashboard');
        }
    }
});