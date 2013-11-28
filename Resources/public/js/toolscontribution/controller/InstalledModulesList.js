/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.module.toolscontribution.controller.InstalledModulesList', {
    extend: 'Ext.app.Controller',

    // override
    init: function() {
        this.callParent(arguments);

        this.control({
            'modera-backend-module-installedmoduleslist': {
                showmoduledetails: this.showModuleDetails,
                showavailablemodules: this.showAvailableModules
            }
        });
    },

    // private
    showModuleDetails: function(panel, record) {
        this.application.getContainer().get('workbench').activateView('module-details-window', { id: record.get('id') });
    },

    // private
    showAvailableModules: function() {
        this.application.getContainer().get('workbench').activateView('available-modules-list');
    }
});