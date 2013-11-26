/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.tools.view.HostPanel', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.modera-backend-tools-hostpanel',

    requires: [
        'Modera.backend.tools.store.Sections'
    ],

    // l10n
    titleText: 'Tools',

    // override
    constructor: function(config) {
        var defaults = {
            store: Ext.create('Modera.backend.tools.store.Sections'),
            border: false,
            columns: [
                { text: 'Name', dataIndex: 'name', flex: 1 }
            ]
        };
        this.config = Ext.apply(defaults, config || {});
        this.callParent([this.config]);

        this.addEvents(
            /**
             * @event changesection
             * @param {Modera.backend.tools.view.HostPanel} me
             */
            'changesection'
        );

        this.assignListeners();
    },

    // private
    assignListeners: function() {
        var me = this;

        this.on('itemclick', function(sm, record) {
            me.fireEvent('changesection', me, record);
        });
    }
});