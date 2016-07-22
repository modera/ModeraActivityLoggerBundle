/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.runtime.ManagerActivity', {
    extend: 'MF.activation.activities.AbstractCompositeActivity',

    /**
     * @private
     * @property {Object[]} sections
     */

    // override
    constructor: function() {
        var me = this;
        me.callParent(arguments);
        me.sections = [];
    },

    // override
    getId: function() {
        return 'manager';
    },

    // override
    isHomeActivity: function() {
        return true;
    },

    // override
    doInit: function(callback) {
        var me = this;
        me.workbench.getService('config_provider').getConfig(function(config) {
            me.sections = [
                {
                    name: 'users',
                    uiClass: 'Modera.backend.security.toolscontribution.runtime.user.ListActivity'
                },
                {
                    name: 'groups',
                    uiClass: 'Modera.backend.security.toolscontribution.runtime.group.ListActivity'
                },
                {
                    name: 'permissions',
                    uiClass: 'Modera.backend.security.toolscontribution.runtime.permission.ListActivity'
                }
            ];
            callback(me);
        });
    },

    // override
    getZones: function(callback) {
        var me = this;

        if (!me.zones) {
            var zone = {
                controllingParam: 'section',
                activities: {},
                controller: function(parentUi, zoneUi, sectionName, callback) {
                    zoneUi.activateSection(sectionName, callback);
                }
            };

            // dynamically populating possible sections
            Ext.each(this.sections, function(sectionConfig) {
                zone.activities[sectionConfig.name] = Ext.create(sectionConfig.uiClass);
            });
            me.zones = [zone];
        }

        callback(me.zones);
    },

    // override
    init: function(executionContext) {
        this.callParent(arguments);
        executionContext.getApplication().loadController('Modera.backend.security.toolscontribution.controller.Manager');
    },

    // override
    doCreateUi: function(params, onReadyCallback) {
        var me = this;

        var panel = Ext.create('Modera.backend.security.toolscontribution.view.Manager', {
            sectionName: params.section
        });
        panel.addSections(me.sections);
        me.setUpZones(panel);
        onReadyCallback(panel);
    },

    // internal
    onSectionLoaded: function(section) {
        section.relayEvents(this.getUi(), ['handleaction']);
    },

    // override
    applyTransition: function(diff, callback) {
        var me = this;

        if (diff.isParamValueChanged(me, 'section')) {
            me.getUi().activateSection(diff.getChangedParamNewValue(me, 'section'), callback);
        } else {
            callback();
        }
    },

    // override
    attachStateListeners: function(ui) {
        var me = this;

        ui.on('sectionchanged', function(sourceComponent, section) {
            me.executionContext.updateParams(me, {
                section: section
            })
        });
    },

    // override
    getDefaultParams: function() {
        return {
            section: 'users'
        }
    }
});