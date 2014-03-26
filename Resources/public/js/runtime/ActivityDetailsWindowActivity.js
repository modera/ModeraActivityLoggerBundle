/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.tools.activitylog.runtime.ActivityDetailsWindowActivity', {
    extend: 'MF.activation.activities.AbstractActivity',

    requires: [
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
        Actions.ModeraBackendToolsActivityLog_Default.get({ id: params.id }, function(response) {
            var grid = Ext.create('MFC.window.ModalWindow', {
                title: 'Activity details',
                maxWidth: 600,
                maxHeight: 500,
                height: 400,
                width: 500
            });

            callback(grid);
        });
    }
});