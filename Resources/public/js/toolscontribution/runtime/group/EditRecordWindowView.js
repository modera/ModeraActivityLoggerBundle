/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.runtime.group.EditRecordWindowView', {
    extend: 'MF.viewsmanagement.views.BasicEditRecordWindowView',

    requires: [
        'Modera.backend.security.toolscontribution.view.group.NewAndEditWindow'
    ],

    // override
    constructor: function(config) {
        var defaults = {
            id: 'edit-group',
            uiFactory: function() {
                return Ext.create('Modera.backend.security.toolscontribution.view.group.NewAndEditWindow', {
                    type: 'edit'
                });
            },
            directClass: Actions.ModeraBackendSecurity_Groups
        };

        this.callParent([Ext.apply(defaults, config || {})]);
    }
});