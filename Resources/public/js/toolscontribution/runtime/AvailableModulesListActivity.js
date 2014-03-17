/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.module.toolscontribution.runtime.AvailableModulesListActivity', {
    extend: 'MF.activation.activities.AbstractActivity',

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

        var previousActivity = null;
        var activities = this.workbench.getActivitiesManager().getActiveActivities();
        if (activities[activities.length - 1]) {
            previousActivity = activities[activities.length - 1];
            previousActivity.getUi().setLoading(this.loadingText);
        }

        panel.getStore().load({
            callback: function() {
                previousActivity.getUi().setLoading(false);
                onReadyCallback(panel);
            }
        });
    }
});