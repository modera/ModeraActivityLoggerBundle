/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.languages.runtime.UserSettingsWindowActivity', {
    extend: 'MF.activation.activities.AbstractActivity',

    requires: [
        'Modera.backend.languages.view.UserSettingsWindow'
    ],

    // override
    getId: function() {
        return 'edit-language';
    },

    // l10n
    windowTitileText: 'Language preference',

    // override
    doCreateUi: function(params, callback) {
        var me = this;

        var window = Ext.create('Modera.backend.languages.view.UserSettingsWindow');

        me.workbench.getService('config_provider').getConfig(function(config) {
            var languagesConfig = config['modera_backend_languages'] || {};
            var languages = languagesConfig['languages'] || [];
            window.down('#languages').getStore().loadData(languages);
        });

        me.userId = params.userId;

        var requestParams = {
            filter: [
                { property: 'user.id', value: 'eq:' + me.userId }
            ],
            hydration: {
                profile: 'main-form'
            }
        };
        Actions.ModeraBackendLanguages_UserSettings.get(requestParams, function(response) {
            window.loadData(response.result);
            callback(window);
        });
    },

    // protected
    attachListeners: function(ui) {
        var me = this;

        ui.on('saveandclose', function(window) {
            var values = window.down('form').getForm().getValues();
            var action = 'update';
            if (!values['id']) {
                var action = 'create';
                values['user'] = me.userId;
            }

            Actions.ModeraBackendLanguages_UserSettings[action]({ record: values }, function(response) {
                if (response.success) {

                    if (me.section) {
                        if ('update' == action) {
                            me.section.fireEvent('recordsupdated', response['updated_models']);
                        } else {
                            me.section.fireEvent('recordscreated', response['created_models']);
                        }
                    }

                    window.close();
                } else {
                    window.showErrors(response);
                }
            });
        })
    }
});