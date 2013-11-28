/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.module.toolscontribution.runtime.InstalledModulesListView', {
    extend: 'MF.viewsmanagement.views.AbstractView',

    requires: [
    ],

    // override
    getId: function() {
        return 'installed-modules-list';
    },

    // override
    isHomeView: function() {
        return true;
    },

    // override
    init: function(executionContext) {
        this.callParent(arguments);

        executionContext.getApplication().loadController('Modera.backend.module.toolscontribution.controller.InstalledModulesList');
    },


    // override
    doCreateUi: function(params, onReadyCallback) {
        var me = this;

        var panel = Ext.create('Modera.backend.module.toolscontribution.view.InstalledModulesList', {});

        panel.getStore().load({
            callback: function() {
                onReadyCallback(panel);
            }
        });
    }
});