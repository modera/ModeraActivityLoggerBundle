/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.runtime.group.NewWindowActivity', {
    extend: 'MF.activation.activities.BasicNewRecordWindowActivity',

    requires: [
        'Modera.backend.security.toolscontribution.view.group.NewAndEditWindow'
    ],

    // override
    getId: function() {
        return 'new-group';
    },

    getSecurityConfig: function() {
        return {
            role: 'ROLE_MANAGE_PERMISSIONS'
        };
    },

    // override
    constructor: function(config) {
        var defaults = {
            id: 'new-group',
            uiFactory: function() {
                return Ext.create('Modera.backend.security.toolscontribution.view.group.NewAndEditWindow', {
                    type: 'new'
                });
            },
            directClass: Actions.ModeraBackendSecurity_Groups
        };

        this.callParent([Ext.apply(defaults, config || {})]);
    }
});