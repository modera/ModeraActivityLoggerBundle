/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.store.UserGroups', {
    extend: 'Ext.data.DirectStore',

    // override
    constructor: function() {
        this.config = {
            fields: [
                'id', 'name'
            ],
            remoteFilter: true,
            proxy: {
                type: 'direct',
                directFn: Actions.ModeraBackendSecurity_Groups.list,
                extraParams: {
                    hydration: {
                        profile: 'compact-list'
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
     * @param {String} userId
     */
    filterByUser: function(userId, exp) {
        this.filters.clear();
        this.filter({ property: 'users', value: (exp || 'in') + ':' + userId });
    }
});