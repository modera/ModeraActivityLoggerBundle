/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.tools.activitylog.runtime.Section', {
    extend: 'MF.runtime.Section',

    requries: [
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

    // override
    handleIntent: function(intent) {
        if ('show_activity_details' == intent.name && intent.params && intent.params.hasOwnProperty('id')) {

            this.application.getContainer().get('workbench').getActivitiesManager().launchActivity(
                'activity-details', { id: intent.params.id }
            );

            return true;
        }
    }
});