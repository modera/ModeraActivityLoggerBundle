/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.module.toolscontribution.store.InstalledModules', {
    extend: 'Ext.data.DirectStore',

    constructor: function() {
        this.config = {
            fields: [
                'id', 'logo', 'name', 'description', 'license',
                'lastVersion', 'currentVersion',
                { name: 'installed', type: 'boolean' },
                { name: 'updateAvailable', type: 'boolean' },
                'isDependency'
            ],
            proxy: {
                type: 'direct',
                directFn: Actions.ModeraBackendModule_Default.getInstalledModules
            }
        };
        this.callParent([this.config]);
    }
});