/**
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */
Ext.define('Modera.backend.dashboard.runtime.Section', {
    extend: 'MF.runtime.Section',

    requires: [
        'Modera.backend.dashboard.runtime.DashboardsView'
    ],

    // override
    activate: function(workbench, callback) {
        var dashboardView = Ext.create('Modera.backend.dashboard.runtime.DashboardsView');

        this.registerViewsManager(workbench, [dashboardView]);

        callback();
    }
});