/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.tools.settings.runtime.DummyActivity', {
    extend: 'MF.activation.activities.AbstractActivity',

    requires: [
        'MF.Util'
    ],

    constructor: function(config) {
        this.config = Ext.apply(this, config);

        this.callParent(arguments);
    },

    // override
    getId: function() {
        return this.config.id;
    },

    // override
    doCreateUi: function(params, callback) {
        var ui = Ext.create('Ext.panel.Panel', {
            html: this.config.text,
            border: true,
            bodyCls: 'container',
            layout: 'fit',
            bbar: [
                '->',
                {
                    scale: 'medium',
                    text: 'Save'
                }
            ]
        });

        Ext.defer(function() {
            callback(ui);
        }, 2000);
    }
});