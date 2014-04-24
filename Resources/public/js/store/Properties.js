/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.configutils.store.Properties', {
    extend: 'Ext.data.Store',

    /**
     * @param {Object} config
     */
    constructor: function(config) {
        var defaults = {
            fields: [
                'id', 'name', 'readableName', 'readableValue', 'value', 'isReadOnly'
            ],
            autoLoad: true,
            proxy: {
                type: 'direct',
                directFn: Actions.ModeraBackendConfigUtils_Default.list,
                reader: {
                    type: 'json',
                    root: 'items'
                },
                extraParams: {
                    hydration: {
                        profile: 'list'
                    },
                    filter: [
                        { property: 'isExposed', value: 'eq:true' }
                    ]
                }
            }
        };

        this.config = Ext.apply(defaults, config || {});
        this.callParent([this.config]);
    }
});