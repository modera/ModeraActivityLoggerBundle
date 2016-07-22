/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.store.Users', {
    extend: 'Ext.data.DirectStore',

    constructor: function() {
        this.config = {
            remoteSort: true,
            remoteFilter: true,
            fields: [
                'id', 'username' , 'email', 'meta',
                'firstName', 'lastName', 'middleName', 'state', 'groups'
            ],
            proxy: {
                type: 'direct',
                directFn: Actions.ModeraBackendSecurity_Users.list,
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