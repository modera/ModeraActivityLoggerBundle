/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.configutils.store.Properties', {
    extend: 'Ext.data.Store',

    /**
     * @param {Object} config
     */
    constructor: function(config) {
        $store = this;

        var defaults = {
            fields: ['id', 'name', 'readableName', 'value', 'isReadOnly'],
            autoLoad: true,
            proxy: {
                type: 'direct',
//                directFn: Actions.ModeraBackendConfigUtils_Default.list,
                api: {
                    read: Actions.ModeraBackendConfigUtils_Default.list,
                    update: Actions.ModeraBackendConfigUtils_Default.update
                },
                reader: {
                    type: 'json',
                    root: 'items'
                },
                writer: {
                    type: 'json',
                    root: 'record'
                },
                extraParams: {
                    hydration: {
                        profile: 'list'
                    }
                }
            }
        };

        this.config = Ext.apply(defaults, config || {});
        this.callParent([this.config]);
    }
});