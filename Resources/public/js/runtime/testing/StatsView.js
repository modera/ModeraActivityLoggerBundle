/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.dashboard.runtime.testing.StatsView', {
    extend: 'MF.viewsmanagement.views.AbstractView',

    // override
    getId: function() {
        return 'stats';
    },

    // override
    doCreateUi: function(params, callback) {
        var me = this;

        var w = Ext.create('Ext.container.Container', {
            html: 'StatsView html'
        });

        Ext.defer(function() {
            callback(w);
        }, 1000);
    }
});