/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.tools.activitylog.runtime.ActivityDetailsWindowActivity', {
    extend: 'MF.activation.activities.AbstractActivity',

    requires: [
        'MFC.window.ModalWindow'
    ],

    // override
    getId: function() {
        return 'activity-details';
    },

    // override
    isHomeActivity: function() {
        return true;
    },

    // override
    doCreateUi: function(params, callback) {
        var me = this;

        var query = {
            filter: [
                { property: 'id', value: 'eq:' + params.id }
            ]
        };
        Actions.ModeraBackendToolsActivityLog_Default.get(query, function(response) {
            if (response.success) {
                var grid = Ext.create('MFC.window.ModalWindow', {
                    title: 'Activity details',
                    maxWidth: 600,
                    maxHeight: 500,
                    height: 400,
                    width: 500,
                    html: response.result.message
                });
            } else {
                throw me.$className + '.doCreateUi(): unsuccessful response received from server: ' + response.message
            }

            callback(grid);
        });
    }
});