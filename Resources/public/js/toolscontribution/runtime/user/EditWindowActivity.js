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

                    if (me.section) {
                        me.section.fireEvent('recordsupdated', response['updated_models']);
                    }

                    window.close();
                } else {
                    window.showErrors(response);
                }
            });
        })
    }
});