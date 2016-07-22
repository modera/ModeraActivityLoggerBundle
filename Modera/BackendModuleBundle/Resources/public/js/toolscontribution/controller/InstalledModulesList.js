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
            },
            'modera-backend-module-installedmoduleslist mfc-header': {
                close: this.onClose
            }
        });
    },

    // private
    onClose: function() {
        this.application.getContainer().get('workbench').activateSection('tools');
    },

    // private
    showModuleDetails: function(panel, record) {
        this.application.getContainer().get('workbench').launchActivity('module-details-window', { id: record.get('id') });
    },

    // private
    showAvailableModules: function() {
        this.application.getContainer().get('workbench').launchActivity('available-modules-list');
    }
});