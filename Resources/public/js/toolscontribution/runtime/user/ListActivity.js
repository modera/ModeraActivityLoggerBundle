/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.runtime.user.ListActivity', {
    extend: 'MF.activation.activities.AbstractActivity',

    requires: [
        'Modera.backend.security.toolscontribution.view.user.List'
    ],

    // override
    getId: function() {
        return 'users';
    },

    getSecurityConfig: function() {
        return {
            role: 'ROLE_ACCESS_BACKEND_TOOLS_SECURITY_SECTION'
        }
    },

    // override
    doCreateUi: function(params, callback) {
        var grid = Ext.create('Modera.backend.security.toolscontribution.view.user.List');

        callback(grid);
    },

    // override
    attachContractListeners: function(ui) {
        var me = this;

        ui.on('newrecord', function(sourceComponent) {
            me.fireEvent('handleaction', 'new-user', sourceComponent);
        });
        ui.on('editrecord', function(sourceComponent, params) {
            me.fireEvent('handleaction', 'edit-user', sourceComponent, params);
        });
        ui.on('deleterecord', function(sourceComponent, params) {
            me.fireEvent('handleaction', 'delete-user', sourceComponent, params);
        });
        ui.on('editpassword', function(sourceComponent, params) {
            me.fireEvent('handleaction', 'edit-password', sourceComponent, params);
        });
        ui.on('editgroups', function(sourceComponent, params) {
            me.fireEvent('handleaction', 'edit-groups', sourceComponent, params);
        });
    }
});