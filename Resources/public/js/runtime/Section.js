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

    // override
    canHandleIntent: function(dispatchedIntent) {
        dispatchedIntent.assertExactName('show_activity_details')
                        .assertHasExactParam('id')
                        .done();
    },

    // override
    handleIntent: function(intent, cb) {
        var me = this;

        me.application.getContainer().get('workbench').getActivitiesManager().launchActivity(
            'activity-details', { id: intent.params.id }
        );

        if (Ext.isFunction(cb)) {
            cb();
        }
    }
});