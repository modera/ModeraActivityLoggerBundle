/**
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

    // override
    getZones: function(callback) {
        if (!this.zones) {
            var activities = {
                dummy1: Ext.create('Modera.backend.tools.settings.runtime.DummyActivity', {
                    id: 'd1',
                    text: 'Dummy1'
                }),
                dummy2: Ext.create('Modera.backend.security.toolscontribution.runtime.user.ListActivity')
            };

            this.zones = [
                {
                    id: 'main',
                    controllingParam: 'section',
                    targetContainerResolver: '#hostPanel',
                    activities: activities,
                    controller: function(rootUi, zoneUi, activityIdToUse, onProcessedCallback) {
                        rootUi.showSection(activityIdToUse);

                        onProcessedCallback();
                    }
                }
            ];
        }

        callback(this.zones);
    },

    // override
    attachListeners: function(ui) {
        ui.on('showsection', function(grid, params) {
            ui.showSection(params.id);
        });
    },

    // override
    doCreateUi: function(params, callback) {
        var grid = Ext.create('Modera.backend.tools.settings.view.HostPanel');

        this.setUpZones(grid);

        callback(grid);
    }
});