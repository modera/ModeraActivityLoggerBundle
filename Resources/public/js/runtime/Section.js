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
                if (view['onSectionLoaded'] && Ext.isFunction(view.onSectionLoaded)) {
                    view.onSectionLoaded(me);
                }

                me.relayEvents(view, ['recordsupdated', 'recordscreated']);
                if (view['getZones'] && Ext.isFunction(view.getZones)) {
                    view.getZones(function(zones) {
                        Ext.each(zones, function(zoneConfig) {

                            Ext.each(Ext.Object.getValues(zoneConfig.activities), function(activity){
                                if (activity['onSectionLoaded'] && Ext.isFunction(activity.onSectionLoaded)) {
                                    activity.onSectionLoaded(me);
                                }

                                me.relayEvents(activity, ['recordsupdated', 'recordscreated']);
                            });

                        });
                    });
                }


            });
        });

        me.configureFlows(workbench);
    },

    // protected
    configureFlows: function(workbench) {
        var me = this;

        if (!me.flowsConfigured) {
            me.on('handleaction', function(actionName, sourceComponent, params) {
                if (workbench.getActivitiesManager().getView(actionName)) {
                    workbench.launchActivity(actionName, params || {});
                } else if (workbench.getSection(actionName)) {
                    workbench.activateSection(actionName, params || {});
                }
            });

            me.flowsConfigured = true;
        }
    }
});