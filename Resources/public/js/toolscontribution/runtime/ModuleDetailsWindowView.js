/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.module.toolscontribution.runtime.ModuleDetailsWindowView', {
    extend: 'MF.viewsmanagement.views.AbstractView',

    requires: [
    ],

    // override
    getId: function() {
        return 'module-details-window';
    },

    // override
    doCreateUi: function(params, callback) {
        Actions.ModeraBackendModule_Default.getModuleDetails({ id: params.id }, function(response) {
            var items = [];
            Ext.iterate(response, function(key, val) {
                items.push({
                    xtype: 'displayfield',
                    fieldLabel: key,
                    value: val
                });
            });

            var w = Ext.create('Ext.window.Window', {
                title: response.name,
                width: 900,
                height: 400,
                modal: true,
                items: {
                    xtype: 'form',
                    bodyPadding: 20,
                    items: items
                }
            });

            w.show();

            callback(w);
        });
    }
});