/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.runtime.user.EditGroupsWindowActivity', {
    extend: 'MF.activation.activities.AbstractActivity',

    requires: [
        'Modera.backend.security.toolscontribution.view.user.EditGroupsWindow'
    ],

    // override
    getId: function() {
        return 'edit-groups';
    },

    getSecurityConfig: function() {
        return {
            role: 'ROLE_MANAGE_USER_PROFILES'
        }
    },

    // override
    doCreateUi: function(params, callback) {
        var window = Ext.create('Modera.backend.security.toolscontribution.view.user.EditGroupsWindow');
        if (Ext.isArray(params.id)) {
            window.loadData({
                id: params.id
            });
            callback(window);
        } else {
            var requestParams = {
                filter: [
                    { property: 'id', value: 'eq:' + params.id }
                ],
                hydration: {
                    profile: 'compact-list'
                }
            };

            Actions.ModeraBackendSecurity_Users.get(requestParams, function(response) {
                window.loadData(response.result);
                callback(window);
            });
        }
    },

    // protected
    attachListeners: function(ui) {
        var me = this;

        ui.on('saveandclose', function(window) {
            var values = window.down('form').getForm().getValues();
            values['groups'] = window.getAssignedGroupsIds();

            var records = [];
            Ext.each(values['id'].split(','), function(id) {
                var data = Ext.clone(values);
                data['id'] = id;
                records.push(data);
            });

            Actions.ModeraBackendSecurity_Users.batchUpdate({ records: records }, function(response) {
                if (response.success) {
                    window.close();
                } else {
                    window.showErrors(response);
                }
            });
        });
    }
});