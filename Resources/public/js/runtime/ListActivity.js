/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.tools.runtime.ListActivity', {
    extend: 'MF.activation.activities.AbstractActivity',

    requires: [
        'Modera.backend.tools.view.HostPanel',
        'Modera.backend.tools.controller.Controller'
    ],

    // override
    getId: function() {
        return 'list';
    },

    // override
    isHomeActivity: function() {
        return true;
    },

    // override
    init: function(executionContext) {
        this.callParent(arguments);

        executionContext.getApplication().loadController('Modera.backend.tools.controller.Controller');
    },

    // override
    doCreateUi: function(params, callback) {
        var panel = Ext.create('Modera.backend.tools.view.HostPanel', {});

        panel.getStore().load({
            callback: function() {
                callback(panel);
            }
        });
    }
});