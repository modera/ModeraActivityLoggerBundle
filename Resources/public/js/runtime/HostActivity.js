/**
 * This activity assumes that runtime config has "settingsSections" configuration property.
 *
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.tools.settings.runtime.HostActivity', {
    extend: 'MF.activation.activities.AbstractCompositeActivity',

    requires: [
        'Modera.backend.tools.settings.view.HostPanel',
        'MF.Util'
    ],

    // override
    getId: function() {
        return 'host';
    },

    // override
    isHomeActivity: function() {
        return true;
    },

    // private
    getSections: function(callback) {
        this.workbench.getService('config_provider').getConfig(function(config) {
            callback(config.settingsSections || []);
        });
    },

    // override
    getZones: function(callback) {
        var me = this;

        if (this.zones) {
            callback(me.zones);
        } else {
            me.getSections(function(sections) {
                var activities = {},
                    indexedParams = {};
                Ext.each(sections, function(section) {
                    var activity = Ext.create(section.activityClass, {
                        id: section.id,
                        name: section.name
                    });

                    activity.getSectionConfig = function() {
                        return Ext.clone(section);
                    };

                    activities[section.id] = activity;

                    if (Ext.isObject(section.meta['activationParams'])) {
                        indexedParams[section.id] = section.meta.activationParams;
                    }
                });

                me.zones = [
                    {
                        id: 'main',
                        controllingParam: 'section',
                        targetContainerResolver: '#hostPanel',
                        activities: activities,
                        paramsFactory: function(contextParams, activity) {
                            if (activity.getSectionConfig) {
                                var id = activity.getSectionConfig().id;
                                return indexedParams[id] ? indexedParams[id] : contextParams;
                            }
                            return contextParams;
                        },
                        controller: function(rootUi, zoneUi, activityIdToUse, onProcessedCallback) {
                            rootUi.showSection(activityIdToUse);

                            onProcessedCallback();
                        }
                    }
                ];
                callback(me.zones);
            });
        }
    },

    // override
    attachListeners: function(ui) {
        ui.on('showsection', function(grid, params) {
            ui.showSection(params.id);
        });
    },

    // override
    doCreateUi: function(params, callback) {
        var me = this;

        this.getSections(function(sections) {
            var grid = Ext.create('Modera.backend.tools.settings.view.HostPanel');
            grid.down('mfc-header').on('close', function() {
                me.workbench.activateSection('tools');
            });

            Ext.each(sections, function(section) {
                grid.addSection({
                    id: section.id,
                    name: section.name,
                    glyph: section.glyph,
                    ui: {
                        layout: 'fit',
                        activity: section.id
                    }
                });
            });

            me.setUpZones(grid);

            callback(grid);
        });
    }
});