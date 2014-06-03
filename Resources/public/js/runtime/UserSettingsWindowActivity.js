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

    // override
    doCreateUi: function(params, callback) {
        var me = this;

        var window = Ext.create('Modera.backend.languages.view.UserSettingsWindow');

        var loadData = function(userId, callback) {
            var requestParams = {
                record: {
                    user: userId
                },
                filter: [
                    { property: 'user.id', value: 'eq:' + userId }
                ],
                hydration: {
                    profile: 'main-form'
                }
            };
            Actions.ModeraBackendLanguages_UserSettings.getOrCreate(requestParams, callback);
        };

        var onLoad = function(data) {
            window.loadData(data);
            callback(window);
        };

        me.workbench.getService('config_provider').getConfig(function(config) {
            var languagesConfig = config['modera_backend_languages'] || {};
            var languages = languagesConfig['languages'] || [];
            window.down('#languages').getStore().loadData(languages);

            if (Ext.isArray(params.userId)) {

                var ids = [];
                Ext.each(params.userId, function(id) {
                    loadData(id, function(response) {
                        if (response.success) {
                            ids.push(response.result['id']);

                            if (params.userId.length == ids.length) {
                                onLoad({
                                    id: ids
                                });
                            }
                        }
                    });
                });
            } else {

                loadData(params.userId, function(response) {
                    if (response.success) {
                        onLoad(response.result);
                    }
                });
            }
        });
    },

    // protected
    attachListeners: function(ui) {
        var me = this;

        ui.on('saveandclose', function(window) {
            var values = window.down('form').getForm().getValues();

            var records = [];
            Ext.each(values['id'].split(','), function(id) {
                var data = Ext.clone(values);
                data['id'] = id;
                records.push(data);
            });

            Actions.ModeraBackendLanguages_UserSettings.batchUpdate({ records: records }, function(response) {
                if (response.success) {
                    window.close();
                } else {
                    window.showErrors(response);
                }
            });
        })
    }
});