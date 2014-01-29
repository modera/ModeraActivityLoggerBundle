/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.module.toolscontribution.runtime.AvailableModulesListView', {
    extend: 'MF.viewsmanagement.views.AbstractView',

    requires: [
    ],

    // l10n
    loadingText: 'Activating module market browser ...',

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

        var previousView = null;
        var views = this.workbench.getViewsManager().getActiveViews();
        if (views[views.length - 1]) {
            previousView = views[views.length - 1];
            previousView.getUi().setLoading(this.loadingText);
        }

        panel.getStore().load({
            callback: function() {
                previousView.getUi().setLoading(false);

                onReadyCallback(panel);

                panel.view.refresh();
            }
        });
    }
});