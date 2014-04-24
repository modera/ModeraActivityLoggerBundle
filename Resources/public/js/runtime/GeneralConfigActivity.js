/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.dcmjr.runtime.GeneralConfigActivity', {
    extend: 'MF.activation.activities.AbstractActivity',

    getId: function() { // override
        return 'general-config';
    },

    doCreateUi: function(params, callback) { // override
        var editorsPool = {
            'site_name': {
                xtype: 'textfield'
            },
            'url': {
                xtype: 'textfield'
            },
            'home_section': {
                xtype: 'textfield'
            }
        };

        var ui = Ext.create('Modera.backend.configutils.view.PropertiesGrid', {
            monitorModel: 'modera.config_bundle.configuration_entry',
            editorsPool: editorsPool
        });

        callback(ui);
    }
});