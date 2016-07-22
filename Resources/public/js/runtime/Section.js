/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.tools.settings.runtime.Section', {
    extend: 'MF.runtime.Section',

    requires: [
        'Modera.backend.tools.settings.runtime.HostActivity'
    ],

    // override
    activate: function(workbench, callback) {
        var me = this;
        var activities = [
            Ext.create('Modera.backend.tools.settings.runtime.HostActivity')
        ];

        me.registerActivitiesManager(workbench, activities, function() {
            callback(function() {
                workbench.getActivitiesManager().iterateActivities(function(activity) {
                    if (activity['getZones'] && Ext.isFunction(activity.getZones)) {
                        activity.getZones(function(zones) {
                            Ext.each(zones, function(zoneConfig) {
                                me.configureInteractions(workbench, zoneConfig.activities);
                            });
                        });
                    }
                });
            });
        });
    },

    // private
    configureInteractions: function(workbench, activities) {
        Ext.each(Ext.Object.getValues(activities), function(activity) {
            activity.on('handleaction', function(actionName, sourceComponent, params) {
                if (this.getSectionConfig) {
                    var id = this.getSectionConfig()['id'] + '-' + actionName;
                    if (workbench.getActivitiesManager().getActivity(id)) {
                        workbench.launchActivity(id, params || {});
                    }
                }
            });
        });
    },

    // override
    registerActivitiesManager: function(workbench, activities, callback) {
        var me = this;

        var parent = me.self.superclass.registerActivitiesManager;
        me.getSharedActivities(function(sharedActivities) {
            parent.apply(me, [workbench, activities.concat(sharedActivities)]);
            callback();
        });
    },

    // protected
    getSharedActivities: function(callback) {
        var me = this;

        if (!callback) {
            return me.callParent();
        }

        var _getSharedActivities = function(sharedActivities, providers, fn) {
            var provider = providers.shift();
            provider.getSharedActivitiesAsync(me, function(activities) {
                sharedActivities = sharedActivities.concat(activities);
                if (providers.length > 0) {
                    _getSharedActivities(sharedActivities, providers, fn);
                } else {
                    fn(sharedActivities);
                }
            });
        };

        var providers = [];
        var sharedActivitiesProviders = me.application.getContainer().getTaggedServices('shared_activities_provider');
        Ext.each(sharedActivitiesProviders, function(provider) {
            if (provider.getSharedActivitiesAsync) {
                providers.push(provider);
            }
        });

        if (providers.length) {
            _getSharedActivities([], providers, callback);
        } else {
            callback([]);
        }
    }
});