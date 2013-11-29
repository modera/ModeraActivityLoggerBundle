/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.module.toolscontribution.runtime.AvailableModulesListView', {
    extend: 'MF.viewsmanagement.views.AbstractView',

    requires: [
    ],

    // override
    getId: function() {
        return 'available-modules-list';
    },

    // override
    init: function(executionContext) {
        this.callParent(arguments);

        executionContext.getApplication().loadController('Modera.backend.module.toolscontribution.controller.AvailableModulesList');
    },

    // override
    doCreateUi: function(params, onReadyCallback) {
        var panel = Ext.create('Modera.backend.module.toolscontribution.view.AvailableModulesList', {});

        panel.getStore().load({
            callback: function() {
                onReadyCallback(panel);
            }
        });
    }
});