/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.dcmjr.runtime.GeneralSiteSettingsActivity', {
    extend: 'MF.activation.activities.AbstractActivity',

    requires: [
        'Modera.backend.dcmjr.view.GeneralSettingsPanel'
    ],

    getId: function() { // override
        return 'general-site-settings';
    },

    doCreateUi: function(params, callback) { // override
        var query = {
            filter: [ { property: 'category', value: 'eq:general' } ],
            hydration: {
                profile: 'list'
            }
        };

        Actions.ModeraBackendConfigUtils_Default.list(query, function(r) {
            var ui = Ext.create('Modera.backend.dcmjr.view.GeneralSettingsPanel');

            Ext.each(r.items, function(item) {
                var cmp = ui.down(Ext.String.format('component[name={0}]', item.name));
                if (cmp) {
                    cmp.serverConfig = item;
                    cmp.setValue(item.value);
                }
            });

            callback(ui);
        });
    },

    // private
    configPropertyValueChanged: function(cmp) {
        var params = {
            record: {
                id: cmp.serverConfig.id,
                value: cmp.getValue()
            }
        };

        Actions.ModeraBackendConfigUtils_Default.update(params, Ext.emtpyFn);
    },

    // override
    attachListeners: function(ui) {
        var me = this;

        Ext.each(ui.query('mfc-inplacefield'), function(f) {
            f.on('editfinished', function(cmp) {
                me.configPropertyValueChanged(cmp);
            });
        });

        Ext.each(ui.query('mfc-switchfield'), function(f) {
            f.on('optionchanged', function(cmp) {
                me.configPropertyValueChanged(cmp);
            });
        });
    }
});