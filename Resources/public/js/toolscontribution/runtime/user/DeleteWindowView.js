/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.runtime.user.DeleteWindowView', {
    extend: 'MF.viewsmanagement.views.BasicDeleteRecordWindowView',

    requires: [
        'MFC.window.DeleteRecordConfirmationWindow'
    ],

    // override
    constructor: function(config) {
        var defaults = {
            id: 'delete-user',
            uiClass: 'MFC.window.DeleteRecordConfirmationWindow',
            directClass: Actions.ModeraBackendSecurity_Users,
            responseRecordNameKey: 'username'
        };

        this.callParent([Ext.apply(defaults, config || {})]);
    }
});