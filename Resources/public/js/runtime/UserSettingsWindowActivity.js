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

        me.workbench.getService('config_provider').getConfig(function(config) {
            var languagesConfig = config['modera_backend_languages'] || {};
            var languages = languagesConfig['languages'] || [];
            window.down('#languages').getStore().loadData(languages);
        });

        if (Ext.isArray(params.userId)) {

            var onLoad = function(ids) {
                window.loadData({
                    id: ids
                });
                callback(window);
            };

            var requestParams = {
                filter: [
                    { property: 'user.id', value: 'in:' + params.userId.join(',') }
                ],
                hydration: {
                    profile: 'main-form'
                }
            };
            Actions.ModeraBackendLanguages_UserSettings.list(requestParams, function(response) {
                var ids = [];
                var users = [];
                Ext.each(response.items, function(item) {
                    ids.push(item['id']);
                    users.push(item['user_id']);
                });

                if (params.userId.length !== users.length) {
                    Ext.each(params.userId, function(id) {
                        if (users.indexOf(id) == -1) {
                            Actions.ModeraBackendLanguages_UserSettings.create({
                                record: {
                                    user: id
                                },
                                hydration: {
                                    profile: 'main-form'
                                }
                            }, function(response) {
                                var item = response.result;
                                ids.push(item['id'])
                                users.push(item['user_id']);
                                if (params.userId.length == users.length) {
                                    onLoad(ids);
                                }
                            });
                        }
                    });
                } else {
                    onLoad(ids);
                }
            });
        } else {

            var requestParams = {
                record: {
                    user: params.userId
                },
                filter: [
                    { property: 'user.id', value: 'eq:' + params.userId }
                ],
                hydration: {
                    profile: 'main-form'
                }
            };
            Actions.ModeraBackendLanguages_UserSettings.getOrCreate(requestParams, function(response) {
                if (response) {
                    window.loadData(response.result);
                }
                callback(window);
            });
        }
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