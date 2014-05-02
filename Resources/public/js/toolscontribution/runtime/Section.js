/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.runtime.Section', {
    extend: 'MF.runtime.MediatorSection',

    // override
    getActivities: function(callback) {
        callback({
            manager: Ext.create('Modera.backend.security.toolscontribution.runtime.ManagerActivity'),

            newuser: Ext.create('Modera.backend.security.toolscontribution.runtime.user.NewWindowActivity'),
            edituser: Ext.create('Modera.backend.security.toolscontribution.runtime.user.EditWindowActivity'),
            editpassword: Ext.create('Modera.backend.security.toolscontribution.runtime.user.PasswordWindowActivity'),
            deleteuser: Ext.create('Modera.backend.security.toolscontribution.runtime.user.DeleteWindowActivity'),
            editgroups: Ext.create('Modera.backend.security.toolscontribution.runtime.user.EditGroupsWindowActivity'),

            newgroup: Ext.create('Modera.backend.security.toolscontribution.runtime.group.NewWindowActivity'),
            deletegroup: Ext.create('Modera.backend.security.toolscontribution.runtime.group.DeleteWindowActivity'),
            editgroup: Ext.create('Modera.backend.security.toolscontribution.runtime.group.EditRecordWindowActivity')
        });
    },

    // override
    activate: function(workbench, callback) {
        var me = this;

        if (!workbench.getPlugin('data-sync')) {
            throw this.$className + '.activate(workbench, callback): No "data-sync" runtime plugin is detected';
        }

        me.getActivities(function(activities) {
            me.registerActivitiesManager(workbench, Ext.Object.getValues(activities));

            callback(function() {
                workbench.getActivitiesManager().iterateActivities(function(activity) {
                    if (activity['getZones'] && Ext.isFunction(activity.getZones)) {
                        activity.getZones(function(zones) {
                            Ext.each(zones, function(zoneConfig) {
                                me.configureInteractions(workbench, zoneConfig.activities);
                            });
                        });
                    }
                });
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