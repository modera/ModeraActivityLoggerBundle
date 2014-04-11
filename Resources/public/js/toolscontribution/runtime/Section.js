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
    }
});