/**
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */
Ext.define('Modera.backend.dashboard.runtime.DashboardListDashboardActivity', {
    extend: 'MF.activation.activities.AbstractActivity',

    require: [
        'Modera.backend.dashboard.view.DefaultDashboardPanel'
    ],

    // override
    getId: function() {
        return 'dashboard-of-dashboards';
    },

    // override
    doCreateUi: function(params, callback) {
        var me = this;

        this.workbench.getService('config_provider').getConfig(function(config) {

            var ui = Ext.create('Modera.backend.dashboard.view.DefaultDashboardPanel', {
                dashboards: config.modera_backend_dashboard.dashboards.filter(function(item) {return !item.default})
            });

            callback(ui);

        });
    },

    // private
    attachListeners: function(ui) {
        var me = this;

        ui.on('changedashboard', function(panel, dashboard) {
            me.workbench.launchActivity('home', {
                name: dashboard.get('name')
            });
        });
    }

});