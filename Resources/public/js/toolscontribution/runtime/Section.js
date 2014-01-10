/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.runtime.Section', {
    extend: 'MF.runtime.Section',

    requires: [
        'Modera.backend.security.toolscontribution.runtime.ManagerView'
    ],

    // protected
    getViews: function() {
        return {
            manager: Ext.create('Modera.backend.security.toolscontribution.runtime.ManagerView', { section: this }),
            newuser: Ext.create('Modera.backend.security.toolscontribution.runtime.user.NewWindowView', { section: this }),
            edituser: Ext.create('Modera.backend.security.toolscontribution.runtime.user.EditWindowView', { section: this }),
            editpassword: Ext.create('Modera.backend.security.toolscontribution.runtime.user.PasswordWindowView', { section: this }),
            deleteuser: Ext.create('Modera.backend.security.toolscontribution.runtime.user.DeleteWindowView', { section: this }),
            editgroups: Ext.create('Modera.backend.security.toolscontribution.runtime.user.EditGroupsWindowView', { section: this }),
            newgroup: Ext.create('Modera.backend.security.toolscontribution.runtime.group.NewWindowView'),
            deletegroup: Ext.create('Modera.backend.security.toolscontribution.runtime.group.DeleteWindowView', { section: this }),
            editgroup: Ext.create('Modera.backend.security.toolscontribution.runtime.group.EditRecordWindowView', { section: this })
        }
    },

    // override
    activate: function(workbench, callback) {
        var me = this;

        if (!workbench.getPlugin('data-sync')) {
            throw this.$className + '.activate(workbench, callback): No "data-sync" runtime plugin is detected';
        }

        var views = me.getViews();

        me.registerViewsManager(workbench, Ext.Object.getValues(views));

        callback(function() {
            workbench.getViewsManager().iterateViews(function(view) {
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
                    workbench.activateView(views[actionName].getId(), params || {});
                }
            });

            me.flowsConfigured = true;
        }
    }
});