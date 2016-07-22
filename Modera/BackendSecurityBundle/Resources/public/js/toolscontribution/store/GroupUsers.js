/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.store.GroupUsers', {
    extend: 'Ext.data.DirectStore',

    // override
    constructor: function() {
        this.config = {
            fields: [
                'id', 'username', 'fullname'
            ],
            remoteFilter: true,
            proxy: {
                type: 'direct',
                directFn: Actions.ModeraBackendSecurity_Users.list,
                extraParams: {
                    hydration: {
                        profile: 'modera-backend-security-group-groupusers'
                    }
                },
                reader: {
                    root: 'items'
                }
            },
            autoLoad: false
        };
        this.callParent([this.config]);
    },

    /**
     * @param {String} groupId
     */
    filterByGroup: function(groupId) {
        this.filters.clear();
        this.filter({ property: 'groups', value: 'in:' + groupId });
    }
});