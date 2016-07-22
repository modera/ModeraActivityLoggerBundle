/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.view.user.EditGroupsWindow', {
    extend: 'MFC.window.NewAndEditRecordWindow',
    alias: 'widget.modera-backend-security-user-editgroupswindow',

    requires: [
        'Modera.backend.security.toolscontribution.store.UserGroups'
    ],

    // l10n
    recordTitle: 'Change group for "{0}"',
    usersCountText: '{0} users',
    availableGroupsText: 'Groups available',
    assignedGroupsText: 'Groups assigned',
    noGroupsText: 'no groups',

    // override
    constructor: function(config) {
        var me = this;

        var defaults = {
            title: me.recordTitle,
            groupName: 'compact-list',
            resizable: false,
            autoScroll: true,
            width: 500,
            maxHeight: Ext.getBody().getViewSize().height - 60,
            items: {
                xtype: 'form',
                layout: {
                    type: 'vbox',
                    align: 'stretch'
                },
                items: [
                    {
                        xtype: 'hiddenfield',
                        name: 'id'
                    },
                    {
                        xtype: 'fieldcontainer',
                        layout: {
                            type: 'hbox',
                            align: 'middle'
                        },
                        items: [
                            {
                                itemId: 'available',
                                xtype: 'grid',
                                border: true,
                                height: 350,
                                multiSelect: true,
                                emptyText: me.noGroupsText,
                                emptyCls: 'mfc-grid-empty-text',
                                columns: [
                                    {
                                        text: me.availableGroupsText,
                                        dataIndex: 'name',
                                        flex: 1
                                    }
                                ],
                                viewConfig: {
                                    plugins: {
                                        ptype: 'gridviewdragdrop',
                                        dragGroup: 'availableGroups',
                                        dropGroup: 'assignedGroups'
                                    }
                                },
                                store: Ext.create('Modera.backend.security.toolscontribution.store.UserGroups'),
                                listeners: {
                                    'afterrender': function(grid) {
                                        grid.view.refresh();
                                    }
                                },
                                flex: 1,
                                tid: 'availablegroups'
                            },
                            {
                                width: 5
                            },
                            {
                                itemId: 'actions',
                                layout: 'vbox',
                                items: [
                                    {
                                        itemId: 'backBtn',
                                        xtype: 'button',
                                        iconCls: 'mfc-icon-back-16',
                                        tid: 'movetoleftbtn'
                                    },
                                    {
                                        height: 5
                                    },
                                    {
                                        itemId: 'forwardBtn',
                                        xtype: 'button',
                                        iconCls: 'mfc-icon-forward-16',
                                        tid: 'movetorightbtn'
                                    }
                                ]
                            },
                            {
                                width: 5
                            },
                            {
                                itemId: 'assigned',
                                xtype: 'grid',
                                border: true,
                                height: 350,
                                multiSelect: true,
                                emptyText: me.noGroupsText,
                                emptyCls: 'mfc-grid-empty-text',
                                columns: [
                                    {
                                        text: me.assignedGroupsText,
                                        dataIndex: 'name',
                                        flex: 1
                                    }
                                ],
                                viewConfig: {
                                    plugins: {
                                        ptype: 'gridviewdragdrop',
                                        dragGroup: 'assignedGroups',
                                        dropGroup: 'availableGroups'
                                    }
                                },
                                store: Ext.create('Modera.backend.security.toolscontribution.store.UserGroups'),
                                listeners: {
                                    'afterrender': function(grid) {
                                        grid.view.refresh();
                                    }
                                },
                                flex: 1
                            }
                        ]
                    }
                ]
            }
        };

        me.config = Ext.apply(defaults, config || {});
        me.callParent([this.config]);

        me.assignListeners();
    },

    loadData: function(data) {
        var me = this;

        me.down('form').getForm().setValues(data);

        if (Ext.isArray(data['id'])) {
            var title = Ext.String.format(me.usersCountText, data['id'].length);
            me.setTitle(Ext.String.format(me.recordTitle, title));
            me.down('#available').getStore().load();
        } else {
            me.setTitle(Ext.String.format(me.recordTitle, data['username']));
            me.down('#available').getStore().filterByUser(data['id'], 'notIn');
            me.down('#assigned').getStore().filterByUser(data['id'], 'in');
        }
    },

    getAssignedGroupsIds: function() {
        var me = this;

        var ids = [];
        me.down('#assigned').getStore().each(function(rec) {
            ids.push(rec.get('id'));
        });

        return ids;
    },

    // private
    assignListeners: function() {
        var me = this;

        Ext.each(me.query('#actions button'), function(btn) {
            btn.on('click', function(btn) {
                if ('forwardBtn' == btn.getItemId()) {
                    var form = me.down('#available');
                    var to = me.down('#assigned');
                } else {
                    var form = me.down('#assigned');
                    var to = me.down('#available');
                }

                var records = form.getSelectionModel().getSelection();
                if (records.length) {
                    form.getStore().remove(records);
                    to.getStore().add(records);
                    to.getSelectionModel().select(records);
                }
            });
        });
    }
});