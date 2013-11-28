/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.module.toolscontribution.runtime.Section', {
    extend: 'MF.runtime.Section',

    requires: [
        'Modera.backend.module.toolscontribution.runtime.AvailableModulesListView',
        'Modera.backend.module.toolscontribution.runtime.InstalledModulesListView',
        'Modera.backend.module.toolscontribution.runtime.ModuleDetailsWindowView'
    ],

    // override
    activate: function(workbench, callback) {
//        workbench.getApplication().loadController('Modera.backend.tools.controller.Controller');

        var installedModuleListView = Ext.create('Modera.backend.module.toolscontribution.runtime.InstalledModulesListView'),
            availableModulesListView = Ext.create('Modera.backend.module.toolscontribution.runtime.AvailableModulesListView'),
            moduleDetailsWindowView = Ext.create('Modera.backend.module.toolscontribution.runtime.ModuleDetailsWindowView');

        this.registerViewsManager(workbench, [availableModulesListView, installedModuleListView, moduleDetailsWindowView]);

        callback();
    }
});