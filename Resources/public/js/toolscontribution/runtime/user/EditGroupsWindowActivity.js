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
        var requestParams = {
            filter: [
                { property: 'id', value: 'eq:' + params.id }
            ],
            hydration: {
                profile: 'compact-list'
            }
        };

        Actions.ModeraBackendSecurity_Users.get(requestParams, function(response) {
            var window = Ext.create('Modera.backend.security.toolscontribution.view.user.EditGroupsWindow');

            window.loadData(response.result);

            callback(window);
        });
    },

    // protected
    attachListeners: function(ui) {
        var me = this;

        ui.on('saveandclose', function(window) {
            var values = window.down('form').getForm().getValues();
            values['groups'] = window.getAssignedGroupsIds()

            Actions.ModeraBackendSecurity_Users.update({ record: values }, function(response) {
                if (response.success) {
                    me.section.fireEvent('recordsupdated', response['updated_models']);

                    window.close();
                } else {
                    window.showErrors(response);
                }
            });
        });
    }
});