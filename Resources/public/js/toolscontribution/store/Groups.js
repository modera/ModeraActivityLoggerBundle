/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.store.Groups', {
    extend: 'Ext.data.DirectStore',

    constructor: function() {
        this.config = {
            fields: [
                'id', 'name', 'usersCount'
            ],
            proxy: {
                type: 'direct',
                directFn: Actions.ModeraBackendSecurity_Groups.list,
                extraParams: {
                    hydration: {
                        profile: 'list'
                    }
                },
                reader: {
                    root: 'items'
                }
            },
            autoLoad: true
        };
        this.callParent([this.config]);
    }
});