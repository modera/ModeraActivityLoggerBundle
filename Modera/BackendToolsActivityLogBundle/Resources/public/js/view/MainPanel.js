/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.tools.activitylog.view.MainPanel', {
    extend: 'Ext.panel.Panel',

    requires: [
        'MFC.Date',
        'MFC.container.Header',
        'MFC.FieldValueChangeMonitor',
        'MFC.form.field.plugin.FieldInputFinishedPlugin',
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
                iconCls: 'modera-backend-tools-activity-log-icon'
            },
            cls: 'modera-backend-tools-activity-grid',
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
                                flex: 1,
                                renderer: function(v, metadata, record) {
                                    metadata.tdCls = 'message ' + record.get('level');
                                    return v;
                                }
                            },
                            {
                                dataIndex: 'createdAt',
                                text: this.timeColumnHeaderText,
                                width: 150,
                                renderer: function(v) {
                                    return MFC.Date.moment(v).fromNow();
                                }
                            }
                        ],
                        store: store,
                        dockedItems: [
                            {
                                docked: 'top',
                                xtype: 'toolbar',
                                items: [
                                    {
                                        width: 300,
                                        itemId: 'authorFilter',
                                        xtype: 'combo',
                                        emptyText: 'User',
                                        hideTrigger: true,
                                        displayField: 'value',
                                        valueField: 'id',
                                        store: Ext.create('Ext.data.Store', {
                                            remoteSort: true,
                                            remoteFilter: true,
                                            fields: [
                                                'id', 'value'
                                            ],
                                            proxy: {
                                                type: 'direct',
                                                directFn: Actions.ModeraBackendToolsActivityLog_Default.suggest,
                                                extraParams: {
                                                    queryType: 'user'
                                                }
                                            }
                                        })
                                    },
                                    {
                                        width: 220,
                                        itemId: 'typeFilter',
                                        xtype: 'combo',
                                        emptyText: 'Event type',
                                        hideTrigger: true,
                                        displayField: 'value',
                                        valueField: 'id',
                                        store: Ext.create('Ext.data.Store', {
                                            remoteSort: true,
                                            remoteFilter: true,
                                            fields: [
                                                'id', 'value'
                                            ],
                                            proxy: {
                                                type: 'direct',
                                                directFn: Actions.ModeraBackendToolsActivityLog_Default.suggest,
                                                extraParams: {
                                                    queryType: 'eventType'
                                                }
                                            }
                                        })
                                    },
                                    {
                                        flex: 1,
                                        itemId: 'messageFilter',
                                        xtype: 'textfield',
                                        emptyText: 'Type here to filter...',
                                        plugins: [Ext.create('MFC.form.field.plugin.FieldInputFinishedPlugin', {
                                            timeout: 800
                                        })],
                                        enableKeyEvents: true,
                                        value: ''
                                    }
                                ]
                            },
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

        this.addEvents(
            /**
             * Event is fired when filter is applied using buttons in activity preview container
             *
             * @event applyFilters
             * @param {Object} values
             * @param {Ext.form.field.ComboBox} authorField
             * @param {Ext.form.field.ComboBox} typeField
             * @param {Ext.form.field.Text} messageFilter
             */
            'applyFilters'
        );

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
                    cls: 'mfc-dashed-top-line',
                    bodyPadding: '20 30 0',
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
                            itemId: 'filterByUserBtn',
                            xtype: 'button',
                            text: this.addAsFilterBtnText,
                            handler: function() {
                                var form = me.down('#activityPreview').getForm();

                                var values = {
                                    author: Ext.decode(form.findField('author').getValue()).id,
                                    type: me.down('#typeFilter').getValue(),
                                    message: me.down('#messageFilter').getValue()
                                };

                                me.fireEvent(
                                    'applyFilters', values,
                                    me.down('#authorFilter'), me.down('#typeFilter'), me.down('#messageFilter')
                                )
                            }
                        },
                        {
                            style: 'margin-top: 5px',
                            fieldLabel: this.eventTypeLabelText,
                            name: 'type'
                        },
                        {
                            xtype: 'button',
                            text: this.addAsFilterBtnText,
                            handler: function() {
                                var form = me.down('#activityPreview').getForm();

                                var values = {
                                    author: me.down('#authorFilter').getValue(),
                                    type: form.findField('type').getValue(),
                                    message: me.down('#messageFilter').getValue()
                                };

                                me.fireEvent(
                                    'applyFilters', values,
                                    me.down('#authorFilter'), me.down('#typeFilter'), me.down('#messageFilter')
                                )
                            }
                        },
                        {
                            style: 'margin-top: 5px',
                            fieldLabel: this.createdAtLabelText,
                            name: 'createdAt',
                            renderer: function(v) {
                                var moment = MFC.Date.moment(v);

                                var info = MFC.Date.format(moment, 'datetime');
                                info += ' (' + moment.fromNow() + ')';

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

        var author = Ext.decode(record.get('author'));
        if (!author.isUser) {
            this.down('#activityPreview #filterByUserBtn').disable();
        }
    },

    // private
    assignListeners: function() {
        var me = this,
            grid = this.down('grid');

        grid.on('itemclick', function(view, record) {
            me.showActivityEntryDetails(record);
        });

        this.on('applyFilters', function(values, authorField, eventTypeField, messageFilter) {
            var filters = [];

            if (values['author']) {
                authorField.getStore().load({
                    params: {
                        queryType: 'exact-user',
                        query:  values.author
                    },
                    callback: function(records) {
                        authorField.setValue(records[0].get('id'));
                    }
                });

                filters.push({ property: 'author', value: 'eq:' + values.author });
            }
            if (values['type']) {
                eventTypeField.setValue(values.type);
                filters.push({ property: 'type', value: 'eq:' + values.type });
            }
            if (values['message']) {
                filters.push({ property: 'message', value: 'like:%' + values.message + '%' });
            }

            var grid = me.down('grid');

            grid.getStore().filters.clear();
            grid.getStore().filter(filters);
        });

        var authorField = this.down('#authorFilter'),
            typeFilter = this.down('#typeFilter'),
            messageFilter = this.down('#messageFilter');

        authorField.on('select', this.onFilterChanged, this);
        this.attachFieldValueMonitor(authorField);
        typeFilter.on('select', this.onFilterChanged, this);
        this.attachFieldValueMonitor(typeFilter);
        messageFilter.on('inputfinished', this.onFilterChanged, this);
    },

    // private
    attachFieldValueMonitor: function(field) {
        var me = this;

        var monitor = Ext.create('MFC.FieldValueChangeMonitor', {
            field: field
        });
        monitor.on('valueChanged', function(field, newValue) {
            if (!newValue) {
                me.onFilterChanged();
            }
        });

        field.on('focus', function(field) {
            field.selectText();
        })
    },

    // private
    onFilterChanged: function() {
        var filterValues = this.getFilterValues();

        var filters = [];
        if (filterValues['author']) {
            filters.push({ property: 'author', value: 'eq:' + filterValues.author });
        }
        if (filterValues['type']) {
            filters.push({ property: 'type', value: 'eq:' + filterValues.type });
        }
        if (filterValues['message']) {
            filters.push({ property: 'message', value: 'like:%' + filterValues.message + '%' });
        }

        var store = this.down('grid').getStore();
        if (0 == filters.length) {
            store.clearFilter();
        } else {
            store.filters.clear();
            store.filter(filters);
        }
    },

    // private
    getFilterValues: function() {
        return {
            author: this.down('#authorFilter').getValue(),
            type: this.down('#typeFilter').getValue(),
            message: this.down('#messageFilter').getValue()
        }
    }
});