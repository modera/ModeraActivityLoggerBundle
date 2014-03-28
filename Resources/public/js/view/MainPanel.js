/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.tools.activitylog.view.MainPanel', {
    extend: 'Ext.panel.Panel',

    requires: [
        'Modera.backend.tools.activitylog.store.Activities',
        'MFC.Date'
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
    userLabelText: 'User',

    constructor: function() {
        var store = Ext.create('Modera.backend.tools.activitylog.store.Activities');

        var defaults = {
            basePanel: true,
            padding: 10,
            tbar: {
                xtype: 'mfc-header',
                title: this.headerTitleText,
                margin: '0 0 9 0',
                iconCls: 'modera-backend-tools-activity-log-icon',
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
                        itemId: 'activitiesGrid',
                        columns: [
                            {
                                dataIndex: 'message',
                                text: this.eventDescriptionColumnHeaderText,
                                flex: 1
                            },
                            {
                                dataIndex: 'createdAt',
                                text: this.timeColumnHeaderText,
                                width: 150,
                                renderer: function(v) {
                                    return MFC.Date.moment(v, 'X').fromNow();
                                }
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
                                fieldLabel: 'Event',
                                itemId: 'eventTypeFilter'
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

    loadActivities: function(params) {
        var store = this.down('grid').getStore();
        if (params['sort-by'] && params['sort-direction']) {
            store.sort([
                { property: params['sort-by'], direction: params['sort-direction'] }
            ]);
        } else {
            store.load();
        }
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
                    itemId: 'activityPreview',
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
                            fieldLabel: this.userLabelText,
                            layout: {
                                type: 'hbox',
                                align: 'stretch'
                            },
                            items: [
                                {
                                    xtype: 'displayfield',
                                    name: 'author',
                                    renderer: function(v) {
                                        v = Ext.decode(v);

                                        if (v.isUser) {
                                            if (v.fullName) {
                                                return Ext.String.format('<b>{0}</b> ( {1} )', v.fullName, v.username)
                                            } else {
                                                return '<b>' + v.username + '</b>';
                                            }
                                        } else {
                                            return '<b>' + v.identity + '</b>';
                                        }
                                    }
                                }
                            ]
                        },
                        {
                            xtype: 'button',
                            text: this.addAsFilterBtnText,
                            handler: function() {
                                var form = me.down('#activityPreview').getForm(),
                                    value = form.findField('user').getValue();

                                me.fireEvent('addfilter', 'user.username', value);
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
                                var form = me.down('#activityPreview').getForm(),
                                    value = form.findField('type').getValue();

                                me.fireEvent('addfilter', 'type', value);
                            }
                        },
                        {
                            fieldLabel: this.createdAtLabelText,
                            name: 'createdAt',
                            renderer: function(v) {
                                var moment = MFC.Date.moment(v);

                                var info = MFC.Date.format(moment, 'datetime');
                                info += ' ( ' + moment.fromNow() + ' ) ';

                                return info;
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

        me.on('addfilter', function(fieldName, value) {
            if ('type' == fieldName) {
                me.down('#eventTypeFilter').setValue(value);

                var store = me.down('#activitiesGrid').getStore();
                store.filters.clear();
                store.filter([
                    { property: 'type', 'value': 'eq:' + value }
                ]);
            }
        });
    }
});