/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.runtime.permission.ListActivity', {
    extend: 'MF.activation.activities.AbstractActivity',

    requires: [
        'Modera.backend.security.toolscontribution.store.Groups',
        'Modera.backend.security.toolscontribution.view.permission.List'
    ],

    // override
    getId: function() {
        return 'permissions';
    },

    // override
    doCreateUi: function(params, callback) {

        var groupsStore = Ext.create('Modera.backend.security.toolscontribution.store.Groups', {
            autoLoad: false
        });
        groupsStore.load({
            callback: function() {

                var grid = Ext.create('Modera.backend.security.toolscontribution.view.permission.List', {
                    groupsStore: groupsStore
                });

                callback(grid);
            }
        });
    },

    // override
    attachStateListeners: function(ui) {
        var me = this;

        ui.on('permissionchange', function(sourceComponent, params) {
            Actions.ModeraBackendSecurity_Permissions.update({ record: params }, function(response) {});
        });
    }
});