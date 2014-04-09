/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.runtime.user.PasswordWindowActivity', {
    extend: 'MF.activation.activities.AbstractActivity',

    requires: [
        'Modera.backend.security.toolscontribution.view.user.PasswordWindow'
    ],

    // override
    getId: function() {
        return 'edit-password';
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
            var window = Ext.create('Modera.backend.security.toolscontribution.view.user.PasswordWindow');

            window.loadData(response.result);

            callback(window);
        });
    },

    // protected
    attachListeners: function(ui) {
        var me = this;

        ui.on('saveandclose', function(window) {
            var values = window.down('form').getForm().getValues();

            Actions.ModeraBackendSecurity_Users.update({ record: values }, function(response) {
                if (response.success) {
                    window.close();
                } else {
                    window.showErrors(response);
                }
            });
        });

        ui.on('generatePassword', function(window) {
            Actions.ModeraBackendSecurity_Users.generatePassword({}, function(response) {
                if (response.success) {
                    window.setPassword(response.result.plainPassword);
                } else {
                    window.showErrors(response);
                }
            });
        });
    }
});