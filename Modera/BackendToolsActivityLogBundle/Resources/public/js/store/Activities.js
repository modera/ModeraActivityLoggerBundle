/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.tools.activitylog.store.Activities', {
    extend: 'Ext.data.DirectStore',

    // override
    constructor: function() {
        this.config = {
            remoteSort: true,
            remoteFilter: true,
            fields: [
                'id', 'author', 'type', 'level', 'message', 'createdAt'
            ],
            proxy: {
                type: 'direct',
                directFn: Actions.ModeraBackendToolsActivityLog_Default.list,
                reader: {
                    root: 'items'
                }
            }
        };
        this.callParent([this.config]);
    }
});