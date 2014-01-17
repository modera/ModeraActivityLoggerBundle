/**
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */
Ext.define('Modera.backend.dashboard.runtime.DashboardsView', {
    extend: 'MF.viewsmanagement.views.AbstractView',

    requires: [
        'Modera.backend.dashboard.view.DashboardPanel'
    ],

    // override
    getId: function() {
        return 'home';
    },

    // override
    isHomeView: function() {
        return true;
    },

    // override
    doCreateUi: function(params, callback) {
        var me = this;
        var ui = Ext.create('Modera.backend.dashboard.view.DashboardPanel', {});

        this.workbench.getService('config_provider').getConfig(function(config) {
            var dashboard_config = config.modera_backend_dashboard;

            if (Ext.isArray(dashboard_config.dashboards)) {
                ui.getStore().loadData(dashboard_config.dashboards);
            }

            var dashboardName = null;

            if (params['name']) {
                dashboardName = params['name']
            } else {
                dashboardName = ui.getStore().findRecord('default', true).get('name');
            }

            /*
             Load dashboard's ui. Do not change, state params.
             */
            ui.setDashboard(dashboardName, function() {
                callback(ui);
            });
        });
    },

    // override
    attachStateListeners: function(ui) {
        var me = this;

        ui.on('changedashboardintention', function(dashboardName) {

            /*
            Load dashboard ui and save dashboard name to state params.
             */
            ui.setDashboard(dashboardName, function() {
                me.executionContext.updateParams(me, {
                    name: dashboardName
                });
            });
        });
    },

    // override
    applyTransition: function(diff, callback) {
        var me = this;

        if (diff.isViewParamValueChanged(this, 'name')) {
            /*
             Load dashboard ui. Save params not needed.
             */
            me.getUi().setDashboard(diff.getViewParamChangedNewValue(this, 'name'), function() {
                callback();
            });
        } else {
            callback();
        }
    }

});