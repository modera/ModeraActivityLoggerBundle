/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.view.group.Overview', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.modera-backend-security-group-overview',

    requires: [
        'Modera.backend.security.toolscontribution.store.Groups',
        'Modera.backend.security.toolscontribution.view.group.GroupUsers',
        'MFC.HasSelectionAwareComponentsPlugin',
        'Ext.menu.Menu'
    ],

    plugins: [Ext.create('MFC.HasSelectionAwareComponentsPlugin', { selector: '#groups' })],

    // l10n
    newGroupBtnText: 'New group',
    editSelectedBtnText: 'Edit selected',
    deleteBtnText: 'Delete',

    // override
    constructor: function(config) {
        var defaults = {
            layout: {
                type: 'hbox',
                align: 'stretch'
            },
            items: [
                {
                    itemId: 'groups',
                    monitorModel: ['modera.security_bundle.group', 'modera.security_bundle.user'],
                    flex: 2,
//                    frame: true,
                    rounded: true,
                    border: true,
                    dockedItems: [
                        {
                            security: {
                                role: 'ROLE_MANAGE_PERMISSIONS',
                                strategy: 'hide'
                            },
                            xtype: 'toolbar',
                            dock: 'top',
                            items: [
                                {
                                    itemId: 'addNewGroupBtn',
                                    text: this.newGroupBtnText,
                                    iconCls: 'mfc-icon-add-24',
                                    scale: 'medium',
                                    security: {
                                        role: 'ROLE_MANAGE_PERMISSIONS',
                                        strategy: 'hide'
                                    }
                                },
                                '->',
                                {
                                    xtype: 'splitbutton',
                                    itemId: 'editGroupBtn',
                                    disabled: true,
                                    text: this.editSelectedBtnText,
                                    iconCls: 'mfc-icon-edit-24',
                                    scale: 'medium',
                                    selectionAware: true,
                                    extensionPoint: 'groupActions',
                                    menu: Ext.create('Ext.menu.Menu', {
                                        items: [
                                            {
                                                itemId: 'deleteBtn',
                                                text: this.deleteBtnText,
                                                scale: 'medium',
                                                iconCls: 'mfc-icon-delete-24',
                                                security: {
                                                    role: 'ROLE_MANAGE_PERMISSIONS',
                                                    strategy: 'hide'
                                                }
                                            }
                                        ]
                                    })
                                }
                            ]
                        }
                    ],
                    xtype: 'grid',
                    hideHeaders: true,
                    columns: [
                        {
                            flex: 1,
                            renderer: function(v, meta, record) {
                                return Ext.String.format(
                                    '{0} <span class="mfc-box-status modera-backend-security-box-status">{1}</span>',
                                    record.get('name'), record.get('usersCount')
                                );
                            }
                        }
                    ],
                    store: Ext.create('Modera.backend.security.toolscontribution.store.Groups')
                },
                {
                    width: 10
                },
                {
                    itemId: 'groupUsers',
                    flex: 3,
                    xtype: 'modera-backend-security-group-groupusers'
                }
            ]
        };

        this.config = Ext.apply(defaults, config || {});
        this.callParent([this.config]);

        this.addEvents(
            /**
             * @event creategroup
             * @param {Modera.backend.security.toolscontribution.view.group.Overview} me
             */
            'creategroup',
            /**
             * @event editgroup
             * @param {Modera.backend.security.toolscontribution.view.group.Overview} me
             * @param {Ext.data.Model} group
             */
            'editgroup',
            /**
             * @event deletegroup
             * @param {Modera.backend.security.toolscontribution.view.group.Overview} me
             * @param {Ext.data.Model} group
             */
            'deletegroup',
            /**
             * @event groupselected
             * @param {Modera.backend.security.toolscontribution.view.group.Overview} me
             * @param {Ext.data.Model} group
             */
            'groupselected'
        );

        this.assignListeners();
    },

    // private
    assignListeners: function() {
        var me = this;

        this.down('#addNewGroupBtn').on('click', function() {
            me.fireEvent('creategroup', me);
        });

        this.down('#editGroupBtn').on('click', function() {
            me.fireEvent('editgroup', me, me.down('#groups').getSelectionModel().getSelection()[0]);
        });

        this.down('#deleteBtn').on('click', function() {
            me.fireEvent('deletegroup', me, me.down('#groups').getSelectionModel().getSelection()[0]);
        });

        this.down('#groups').on('selectionchange', function(sm, records) {
            if (1 == records.length) {
                me.fireEvent('groupselected', me, records[0]);
            } else {
                me.down('#groupUsers').getLayout().setActiveItem('placeholder');
            }
        });

        this.relayEvents(me.down('#groupUsers'), [
            'editrecord', 'deleterecord', 'editgroups'
        ]);
    },

    /**
     * @param {String} groupId
     * @param {String} groupName
     */
    showGroupUsers: function(groupId, groupName) {
        this.down('#groupUsers').getLayout().setActiveItem('users');

        var ui = this.down('#groupUsers #users');

        ui.setTitle(groupName);
        ui.getStore().filterByGroup(groupId);
    }
});