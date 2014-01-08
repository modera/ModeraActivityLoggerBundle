/**
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */
Ext.define('Modera.backend.dashboard.view.DashboardPanel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.modera-backdashboard-dashboardpanel',

    requires: [
        'Modera.backend.dashboard.store.Dashboards',
        'MFC.container.Header',
    ],

    // l10n
    nothingToDisplayText: 'No dashboards',
    titleText: 'Dashboard',

    // override
    constructor: function(config) {
        var me = this;
        var defaults = {
            basePanel: true,
            padding: 10,
            border: true,

            ui: 'rounded',
            boxShadow: true,

//            items: [

//
//                {
//                    title: 'Hello Ext',
//                    html : 'Hello! Welcome to Ext JS.'
//                }
//            ],
            dockedItems: [
                {
                    xtype: 'mfc-header',
                    dock: 'top',
                    title: me.titleText,
                    margin: '0 0 9 0',
                    items: [
                        '->',
                        {
                            xtype: 'combo',
                            itemId: 'dashboard-select',
                            fieldLabel: 'Select dashboard',
                            displayField: 'label',
                            valueField: 'name',
                            width: 500,
                            labelWidth: 130,
                            store: Ext.create('Modera.backend.dashboard.store.Dashboards')

        //                    listeners: {
        //                        select: function() {
        //                            Ext.Msg.alert('Chosen book', 'Buying ISBN: ' + this.getValue());
        //                        }
        //                    }
                        }
                    ]
                }
            ]
        };
        this.config = Ext.apply(defaults, config || {});
        this.callParent([this.config]);

//        this.addEvents(
//            /**
//             * @event changesection
//             * @padashboardModera.backend.dashboard.view.HostPanel} me
//             */
//            'changesection'
//        );

        this.assignListeners();
    },

    getStore: function() {
        var me = this;
        return me.down('#dashboard-select').getStore();
    },

    // private
    assignListeners: function() {
        var me = this;
        me.down('#dashboard-select').on('select', function(combo, selections) {
            if (selections.length) {
                var record = selections[0];
                me.fireEvent('changedashboardintention', record.get('name'));
            }
        });
    },

    setDashboard: function(dashboardName, callback) {

        var me = this;
        var record = me.getStore().findRecord('name', dashboardName);

        var uiClass = record.get('uiClass');

        Ext.require(uiClass, function() {
            me.removeAll();
            me.add(Ext.create(uiClass));

            me.down('#title').update(record.get('label'));
            me.down('#dashboard-select').setValue(record);

            me.doLayout();

            callback();
        })
    }
});