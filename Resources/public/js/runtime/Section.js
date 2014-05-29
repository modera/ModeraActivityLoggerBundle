/**
 * @author Alex Rudakov <alexandr.rudakov@modera.org>
 */
Ext.define('Modera.backend.dashboard.runtime.Section', {
    extend: 'MF.runtime.Section',

    requires: [
        'Modera.backend.dashboard.runtime.DashboardsActivity'
    ],

    // override
    getViews: function() {
        return {
            dashboard: Ext.create('Modera.backend.dashboard.runtime.DashboardsActivity')
        }
    },

    // override
    activate: function(workbench, callback) {
        var me = this;

        if (!workbench.getPlugin('data-sync')) {
            throw this.$className + '.activate(workbench, callback): No "data-sync" runtime plugin is detected';
        }

        var views = me.getViews();

        me.registerActivitiesManager(workbench, Ext.Object.getValues(views));

        callback(function() {
            workbench.getActivitiesManager().iterateActivities(function(view) {
                if (view['getZones'] && Ext.isFunction(view.getZones)) {
                    view.getZones(function(zones) {
                        Ext.each(zones, function(zoneConfig) {
                            me.configureInteractions(workbench, zoneConfig.activities);
                        });
                    });
                }
            });
        });
    },

    // private
    configureInteractions: function(workbench, activities) {
        var me = this;
        Ext.each(Ext.Object.getValues(activities), function(activity) {
            activity.on('handleaction', function(actionName, sourceComponent, params) {
                if (workbench.getActivitiesManager().getActivity(actionName)) {
                    workbench.launchActivity(actionName, params || {});
                } else if (workbench.getSection(actionName)) {
                    workbench.activateSection(actionName, params || {});
                }
            });
        });
    }
});