/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.tools.activitylog.view.MainPanel', {
    extend: 'Ext.panel.Panel',

    requires: [
        'Modera.backend.tools.activitylog.store.Activities'
    ],

    // l10n
    headerTitleText: 'Activity log',
    eventDescriptionColumnHeaderText: 'Event description',
    timeColumnHeaderText: 'Time',
    addAsFilterBtnText: 'Add as filter',
    eventTypeLabelText: 'Event',
    createdAtLabelText: 'Time',
    levelLabelText: 'Level',
    eventLabelText: 'Event description',
    detailsLabelText: 'Details...',

    constructor: function() {
        var store = Ext.create('Modera.backend.tools.activitylog.store.Activities');

        var defaults = {
            basePanel: true,
            padding: 10,
            tbar: {
                xtype: 'mfc-header',
                title: this.headerTitleText,
                margin: '0 0 9 0',
                iconCls: 'modera-backend-security-tools-icon',
                closeBtn: true
            },
            layout: 'fit',
            items: {
                layout: 'border',
                items: [
                    {
                        border: true,
                        layout: 'fit',
                        rounded: true,
                        region: 'center',
                        xtype: 'grid',
                        columns: [
                            {
                                dataIndex: 'message',
                                text: this.eventDescriptionColumnHeaderText,
                                flex: 1
                            },
                            {
                                dataIndex: 'createdAt',
                                text: this.timeColumnHeaderText,
                                width: 150
                            }
                        ],
                        store: store,
                        tbar: [
                            {
                                xtype: 'textfield',
                                fieldLabel: 'User'
                            },
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Event'
                            }
                        ],
                        dockedItems: [
                            {
                                xtype: 'pagingtoolbar',
                                store: store,
                                dock: 'bottom',
                                displayInfo: true
                            }
                        ]
                    },
                    {
                        itemId: 'detailsContainer',
                        width: 400,
                        region: 'east'
                    }
                ]
            }
        };

        this.callParent([defaults]);

        this.assignListeners();
    },

    // private
    showActivityEntryDetails: function(record) {
        var me = this,
            container = this.down('#detailsContainer');

        var detailsForm = Ext.create('Ext.form.Panel', {
            layout: {
                type: 'vbox',
                align: 'stretch'
            },
            items: [
                {
                    style: {
                        'padding-top': '20px',
                        'border-top': '2px dashed #ECECEC'
                    },
                    bodyPadding: '0 30',
                    border: false,
                    xtype: 'form',
                    defaultType: 'displayfield',
                    defaults: {
                        labelAlign: 'top',
                        labelStyle: 'color: #B2B2B2; font-weight: normal; margin-bottom: -2px;'
                    },
                    autoScroll: true,
                    items: [
                        {
                            xtype: 'fieldcontainer',
                            fieldLabel: 'User',
                            layout: {
                                type: 'hbox',
                                align: 'stretch'
                            },
                            items: [
                                {
                                    xtype: 'displayfield',
                                    name: 'author',
                                    renderer: function(value) {
                                        return '<b>' + value + '</b>';
                                    },
                                    listeners: {
                                        change: function(field) {
                                            field.up('fieldcontainer').setHeight('auto');
                                        }
                                    }
                                },
                                {
                                    xtype: 'displayfield',
                                    name: 'username',
                                    renderer: function(value) {
                                        return value ? '&nbsp; (' + value + ')' : '';
                                    }
                                }
                            ]
                        },
                        {
                            xtype: 'button',
                            text: this.addAsFilterBtnText,
                            handler: function() {
                                var form = me.query('#activityPreview')[0].getForm();
                                var value = form.findField('user').getValue();
                                me.fireEvent('addAsFilter', 'user.username', value);
                            }
                        },
                        {
                            fieldLabel: this.eventTypeLabelText,
                            name: 'type'
                        },
                        {
                            xtype: 'button',
                            text: this.addAsFilterBtnText,
                            handler: function() {
                                var form = me.query('#activityPreview')[0].getForm();
                                var value = form.findField('type').getValue();
                                me.fireEvent('addAsFilter', 'type', value);
                            }
                        },
                        {
                            fieldLabel: this.createdAtLabelText,
                            name: 'createdAt',
                            renderer: function(value) {
                                return value;
//                                var date = new Date(value);
//                                var info = MFC.Date.moment(date).format('DD.MM.YYYY HH:MM');
//                                info += ' (' + MFC.Date.moment(date).fromNow() + ')';
//
//                                return info;
                            }
                        },
                        {
                            fieldLabel: this.levelLabelText,
                            name: 'level'
                        },
                        {
                            fieldLabel: this.eventLabelText,
                            name: 'message',
                            fieldStyle: {
                                display: 'block',
                                overflow: 'hidden',
                                maxHeight: '250px'
                            }
                        },
                        {
                            xtype: 'button',
                            text: this.detailsLabelText,
                            handler: function() {
                                var selection = me.down('grid').getSelectionModel().getSelection();
                                if (selection[0]) {
                                    me.fireEvent('showactivityentrydetails', me, selection[0].data);
                                }
                            }
                        }
                    ]
                }
            ]
        });
        detailsForm.loadRecord(record);

        container.removeAll();
        container.add(detailsForm);
    },

    // private
    assignListeners: function() {
        var me = this,
            grid = this.down('grid');

        grid.on('itemclick', function(view, record) {
            me.showActivityEntryDetails(record);
        });
    }
});