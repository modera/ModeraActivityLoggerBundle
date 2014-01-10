/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.store.Users', {
    extend: 'Ext.data.DirectStore',

    constructor: function() {
        this.config = {
            fields: [
                'id', 'username' , 'email',
                'firstName', 'lastName', 'middleName', 'groups'
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