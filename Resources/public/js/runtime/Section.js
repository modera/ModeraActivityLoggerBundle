/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.tools.activitylog.runtime.Section', {
    extend: 'MF.runtime.Section',

    requires: [
        'Modera.backend.tools.activitylog.runtime.ListActivity',
        'Modera.backend.tools.activitylog.runtime.ActivityDetailsWindowActivity'
    ],

    // override
    activate: function(workbench, callback) {
        var activities = [
            Ext.create('Modera.backend.tools.activitylog.runtime.ListActivity'),
            Ext.create('Modera.backend.tools.activitylog.runtime.ActivityDetailsWindowActivity')
        ];

        this.registerActivitiesManager(workbench, activities);

        callback();
    },

    canHandleIntent: function(intent, cb) {
        cb('show_activity_details' == intent.name && intent.params && intent.params.hasOwnProperty('id'));
    },

    // override
    handleIntent: function(intent) {
        var me = this;
        this.canHandleIntent(intent, function(result) {
            if (result) {
                me.application.getContainer().get('workbench').getActivitiesManager().launchActivity(
                    'activity-details', { id: intent.params.id }
                );

                cb();
            }
        });
    }
});