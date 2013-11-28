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
            var w = Ext.create('Ext.window.Window', {
                title: 'Modules details: ' + response.name,
                width: 400,
                height: 250
            });

            w.show();

            callback(w);
        });
    }
});