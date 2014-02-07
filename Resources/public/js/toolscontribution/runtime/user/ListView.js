/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.runtime.user.ListView', {
    extend: 'MF.viewsmanagement.views.AbstractView',

    requires: [
        'Modera.backend.security.toolscontribution.view.user.List'
    ],

    // override
    getId: function() {
        return 'users';
    },

    // override
    doCreateUi: function(params, callback) {
        var grid = Ext.create('Modera.backend.security.toolscontribution.view.user.List');

        callback(grid);
    }
});