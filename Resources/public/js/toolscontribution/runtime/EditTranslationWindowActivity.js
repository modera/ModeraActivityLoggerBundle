/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.translationstool.toolscontribution.runtime.EditTranslationWindowActivity', {
    extend: 'MF.activation.activities.AbstractActivity',

    requires: [
        'Modera.backend.translationstool.toolscontribution.view.EditTranslationWindow'
    ],

    // override
    getId: function() {
        return 'edit-translation';
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

        Actions.ModeraBackendTranslationsTool_LanguageTranslations.get(requestParams, function(response) {
            var window = Ext.create('Modera.backend.translationstool.toolscontribution.view.EditTranslationWindow');

            window.loadData(response.result);

            callback(window);
        });
    },

    // protected
    attachListeners: function(ui) {
        var me = this;

        ui.on('saveandclose', function(window) {
            var values = window.down('form').getForm().getValues();
            values['isNew'] = false;

            Actions.ModeraBackendTranslationsTool_LanguageTranslations.update({ record: values }, function(response) {
                if (response.success) {

                    if (me.section) {
                        me.section.fireEvent('recordsupdated', response['updated_models']);
                    }

                    window.close();
                } else {
                    window.showErrors(response);
                }
            });
        });
    }
});