/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.module.toolscontribution.store.AvailableModules', {
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
                directFn: Actions.ModeraBackendModule_Default.getAvailableModules
            }
        };
        this.callParent([this.config]);
    }
});