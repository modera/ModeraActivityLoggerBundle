/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.runtime.user.NewWindowActivity', {
    extend: 'MF.activation.activities.AbstractActivity',

    requires: [
        'Modera.backend.security.toolscontribution.view.user.NewWindow'
    ],

    // override
    getId: function() {
        return 'new-user';
    },

    getSecurityConfig: function() {
        return {
            role: 'ROLE_MANAGE_USER_PROFILES'
        }
    },

    // override
    doCreateUi: function(params, callback) {
        var window = Ext.create('Modera.backend.security.toolscontribution.view.user.NewWindow');

        callback(window);
    },

    // override
    attachListeners: function(window) {
        var me = this;

        window.on('saveandclose', function() {
            var values = window.down('form').getForm().getValues();

            Actions.ModeraBackendSecurity_Users.create({ record: values }, function(response) {
                if (response.success) {
                    if (response['updated_models']) {
                        me.fireEvent('recordsupdated', response['updated_models']);
                    }
                    if (response['created_models']) {
                        me.fireEvent('recordscreated', response['created_models']);
                    }

                    window.close();
                } else {
                    window.showErrors(response);
                }
            });
        });
    }
});