/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.runtime.Section', {
    extend: 'MF.runtime.Section',

    requires: [
        'Modera.backend.security.toolscontribution.runtime.ManagerActivity'
    ],

    // protected
    getActivities: function() {
        return {
            manager: Ext.create('Modera.backend.security.toolscontribution.runtime.ManagerActivity', { section: this }),

            newuser: Ext.create('Modera.backend.security.toolscontribution.runtime.user.NewWindowActivity', { section: this }),
            edituser: Ext.create('Modera.backend.security.toolscontribution.runtime.user.EditWindowActivity', { section: this }),
            editpassword: Ext.create('Modera.backend.security.toolscontribution.runtime.user.PasswordWindowActivity', { section: this }),
            deleteuser: Ext.create('Modera.backend.security.toolscontribution.runtime.user.DeleteWindowActivity', { section: this }),
            editgroups: Ext.create('Modera.backend.security.toolscontribution.runtime.user.EditGroupsWindowActivity', { section: this }),

            newgroup: Ext.create('Modera.backend.security.toolscontribution.runtime.group.NewWindowActivity'),
            deletegroup: Ext.create('Modera.backend.security.toolscontribution.runtime.group.DeleteWindowActivity', { section: this }),
            editgroup: Ext.create('Modera.backend.security.toolscontribution.runtime.group.EditRecordWindowActivity', { section: this })
        }
    },

    // override
    activate: function(workbench, callback) {
        var me = this;

        if (!workbench.getPlugin('data-sync')) {
            throw this.$className + '.activate(workbench, callback): No "data-sync" runtime plugin is detected';
        }

        var views = me.getActivities();

        me.registerActivitiesManager(workbench, Ext.Object.getValues(views));

        callback(function() {
            workbench.getActivitiesManager().iterateActivities(function(view) {
                if (view['onSectionLoaded'] && Ext.isFunction(view.onSectionLoaded)) {
                    view.onSectionLoaded(me);
                }

                me.relayEvents(view, ['recordsupdated', 'recordscreated']);
            });
        });

        me.configureFlows(workbench, views);
    },

    // protected
    configureFlows: function(workbench, views) {
        var me = this;

        if (!me.flowsConfigured) {
            me.on('handleaction', function(actionName, sourceComponent, params) {
                if (views[actionName]) {
                    workbench.launchActivity(views[actionName].getId(), params || {});
                }
            });

            me.flowsConfigured = true;
        }
    }
});