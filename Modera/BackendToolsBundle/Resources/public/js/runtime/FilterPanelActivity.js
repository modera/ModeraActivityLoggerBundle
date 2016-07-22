/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.tools.runtime.UploadingWindowActivity', {
    extend: 'MF.activation.activities.AbstractActivity',

    requires: [
        'Modera.backend.tools.view.HostPanel',
        'Modera.backend.tools.controller.Controller'
    ],

    // override
    getId: function() {
        return 'filter-panel';
    },

    // override
    doCreateUi: function(params, callback) {
        var ui = Ext.create('Ext.panel.Panel', {
            border: false,
            ui: 'rounded',
            boxShadow: true,
            layout: 'fit',
            items: {

            }
        });

        callback(w);
    }
});