/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.tools.activitylog.runtime.ListActivity', {
    extend: 'MF.activation.activities.AbstractActivity',

    requires: [
        'Modera.backend.tools.activitylog.view.MainPanel',
        'MF.Util'
    ],

    // override
    getId: function() {
        return 'list';
    },

    // override
    isHomeActivity: function() {
        return true;
    },

    // override
    doCreateUi: function(params, callback) {
        var grid = Ext.create('Modera.backend.tools.activitylog.view.MainPanel', {

        });

        callback(grid);
    },

    // override
    attachListeners: function(ui) {
        var intentMgr = this.workbench.getService('intent_manager');

        ui.on('showactivityentrydetails', function(panel, data) {
            intentMgr.dispatch({
                name: 'show_activity_details',
                params: data
            });
        });
    }
});