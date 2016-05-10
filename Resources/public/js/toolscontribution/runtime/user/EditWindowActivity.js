/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.runtime.user.EditWindowActivity', {
    extend: 'MF.activation.activities.AbstractActivity',

    requires: [
        'Modera.backend.security.toolscontribution.view.user.EditWindow'
    ],

    // override
    getId: function() {
        return 'edit-user';
    },

    getSecurityConfig: function() {
        var me = this;

        return {
            handler: function(continueCallback, params, sm, showDialog) {
                var configProvider = me.executionContext.getApplication().getContainer().get('config_provider');
                configProvider.getConfig(function(config) {
                    // user always should be able to edit his own profile
                    if (config.userProfile.id == params.id) {
                        continueCallback();
                    } else {
                        // and will be edit other people's profiles only when he has this security role
                        sm.isAllowed('ROLE_MANAGE_USER_PROFILES', function(result) {
                            if (result) {
                                continueCallback();
                            } else {
                                showDialog();
                            }
                        });
                    }
                });
            }
        };
    },

    // override
    doCreateUi: function(params, callback) {
        var requestParams = {
            filter: [
                { property: 'id', value: 'eq:' + params.id }
            ],
            hydration: {
                profile: 'main-form'
            }
        };

        Actions.ModeraBackendSecurity_Users.get(requestParams, function(response) {
            var window = Ext.create('Modera.backend.security.toolscontribution.view.user.EditWindow');

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

    }
});