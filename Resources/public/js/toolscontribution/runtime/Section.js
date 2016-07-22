/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.translationstool.toolscontribution.runtime.Section', {
    extend: 'MF.runtime.Section',

    // protected
    getActivities: function() {
        return {
            list: Ext.create('Modera.backend.translationstool.toolscontribution.runtime.ListActivity', { section: this }),
            edittranslation: Ext.create('Modera.backend.translationstool.toolscontribution.runtime.EditTranslationWindowActivity', { section: this })
        }
    },

    // override
    activate: function(workbench, callback) {
        var me = this;

        if (!workbench.getPlugin('data-sync')) {
            throw this.$className + '.activate(workbench, callback): No "data-sync" runtime plugin is detected';
        }

        var activities = me.getActivities();

        me.registerActivitiesManager(workbench, Ext.Object.getValues(activities));

        callback(function() {
            workbench.getActivitiesManager().iterateActivities(function(activity) {
                if (activity['onSectionLoaded'] && Ext.isFunction(activity.onSectionLoaded)) {
                    activity.onSectionLoaded(me);
                }

                me.relayEvents(activity, ['recordsupdated', 'recordscreated']);
            });
        });

        me.configureFlows(workbench, activities);
    },

    // protected
    configureFlows: function(workbench, activities) {
        var me = this;
        if (!me.flowsConfigured) {
            me.on('handleaction', function(actionName, sourceComponent, params) {
                if ('close' == actionName) {
                    workbench.activateSection('tools');
                }
                else if (activities[actionName]) {
                    workbench.launchActivity(activities[actionName].getId(), params || {});
                }
            });

            me.flowsConfigured = true;
        }
    }
});