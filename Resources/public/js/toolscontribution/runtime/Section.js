/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.module.toolscontribution.runtime.Section', {
    extend: 'MF.runtime.Section',

    requires: [
        'Modera.backend.module.toolscontribution.runtime.AvailableModulesListActivity',
        'Modera.backend.module.toolscontribution.runtime.InstalledModulesListActivity',
        'Modera.backend.module.toolscontribution.runtime.ModuleDetailsWindowActivity'
    ],

    // override
    activate: function(workbench, callback) {
//        workbench.getApplication().loadController('Modera.backend.tools.controller.Controller');

        var installedModuleListActivity = Ext.create('Modera.backend.module.toolscontribution.runtime.InstalledModulesListActivity'),
            availableModulesListActivity = Ext.create('Modera.backend.module.toolscontribution.runtime.AvailableModulesListActivity'),
            moduleDetailsWindowActivity = Ext.create('Modera.backend.module.toolscontribution.runtime.ModuleDetailsWindowActivity');

        this.registerActivitiesManager(workbench, [availableModulesListActivity, installedModuleListActivity, moduleDetailsWindowActivity]);

        callback();
    }
});