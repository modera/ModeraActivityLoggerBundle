/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.runtime.group.ListActivity', {
    extend: 'MF.activation.activities.AbstractActivity',

    requires: [
        'Modera.backend.security.toolscontribution.view.group.Overview'
    ],

    // override
    getId: function() {
        return 'groups';
    },

    // override
    init: function(executionContext) {
        this.callParent(arguments);

        executionContext.getApplication().loadController('Modera.backend.security.toolscontribution.controller.Groups');
    },

    // override
    doCreateUi: function(params, callback) {
        var grid = Ext.create('Modera.backend.security.toolscontribution.view.group.Overview');

        callback(grid);
    }
});