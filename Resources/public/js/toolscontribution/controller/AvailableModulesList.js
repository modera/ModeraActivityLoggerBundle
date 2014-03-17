/**
 * @author Sergei Vizel <sergei.vizle@modera.org>
 */
Ext.define('Modera.backend.module.toolscontribution.controller.AvailableModulesList', {
    extend: 'Ext.app.Controller',

    // override
    init: function() {
        this.callParent(arguments);

        this.control({
            'modera-backend-module-availablemoduleslist': {
                showmoduledetails: this.showModuleDetails,
                showinstalledmodules: this.showInatalledModules
            }
        });
    },

    // private
    showModuleDetails: function(panel, record) {
        this.application.getContainer().get('workbench').launchActivity('module-details-window', { id: record.get('id') });
    },

    // private
    showInatalledModules: function() {
        this.application.getContainer().get('workbench').launchActivity('installed-modules-list');
    }
});