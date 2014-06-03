/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.view.user.List', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.modera-backend-security-user-list',

    requires: [
        'Modera.backend.security.toolscontribution.store.Users',
        'MFC.HasSelectionAwareComponentsPlugin',
        'Ext.menu.Menu'
    ],

    plugins: [Ext.create('MFC.HasSelectionAwareComponentsPlugin')],

    // l10n
    firstNameColumnHeaderText: 'First name',
    lastNameColumnHeaderText: 'Last name',
    usernameColumnHeaderText: 'Principal',
    emailColumnHeaderText: 'Email',
    stateColumnHeaderText: 'State',
    groupsColumnHeaderText: 'Membership',
    addBtnText: 'New user',
    editBtnText: 'Edit selected',
    groupsBtnText: 'Group membership...',
    changePasswordBtnText: 'Change password',
    deleteBtnText: 'Delete',
    stateNewText: 'New',
    stateActiveText: 'Active',

    // override
    constructor: function(config) {
        var me = this;

        config = config || {};

        var store = config.store || Ext.create('Modera.backend.security.toolscontribution.store.Users');

        var defaults = {
            rounded: true,
            border: true,
            monitorModel: 'modera.security_bundle.user',
            emptyCls: 'mfc-grid-empty-text',
            store: store,
            selType: 'checkboxmodel',
            columns: [
                {
                    width: 160,
                    text: me.firstNameColumnHeaderText,
                    dataIndex: 'firstName',
                    renderer: me.defaultRenderer()
                },
                {
                    width: 160,
                    text: me.lastNameColumnHeaderText,
                    dataIndex: 'lastName',
                    renderer: me.defaultRenderer()
                },
                {
                    width: 160,
                    text: me.usernameColumnHeaderText,
                    dataIndex: 'username',
                    renderer: me.defaultRenderer()
                },
                {
                    width: 260,
                    text: me.emailColumnHeaderText,
                    dataIndex: 'email',
                    renderer: me.defaultRenderer()
                },
                {
                    width: 80,
                    text: me.stateColumnHeaderText,
                    dataIndex: 'state',
                    renderer: function(v) {
                        var state = 1 === v ? 'Active' : 'New';
                        return me['state' + state + 'Text'];
                    }
                },
                {
                    flex: 1,
                    sortable: false,
                    text: me.groupsColumnHeaderText,
                    dataIndex: 'groups',
                    renderer: me.defaultRenderer()
                }
            ],
            dockedItems: [
                {
                    security: {
                        role: 'ROLE_MANAGE_USER_PROFILES',
                        strategy: 'hide'
                    },
                    xtype: 'toolbar',
                    dock: 'top',
                    items: [
                        {
                            hidden: config.hideViewAwareComponents || false,
                            itemId: 'newRecordBtn',
                            iconCls: 'mfc-icon-add-24',
                            text: me.addBtnText,
                            scale: 'medium',
                            security: {
                                role: 'ROLE_MANAGE_USER_PROFILES',
                                strategy: 'hide'
                            }
                        },
                        '->',
                        {
                            xtype: 'splitbutton',
                            disabled: true,
                            selectionAware: true,
                            multipleSelectionSupported: true,
                            itemId: 'editRecordBtn',
                            iconCls: 'mfc-icon-edit-24',
                            text: me.editBtnText,
                            scale: 'medium',
                            extensionPoint: 'userActions',
                            menu: Ext.create('Ext.menu.Menu', {
                                items: [
                                    {
                                        itemId: 'deleteBtn',
                                        text: me.deleteBtnText,
                                        scale: 'medium',
                                        iconCls: 'mfc-icon-delete-24'
                                    }
                                ]
                            })
                        },
                        {
                            disabled: true,
                            selectionAware: true,
                            multipleSelectionSupported: true,
                            itemId: 'editGroupsBtn',
                            iconCls: 'modera-backend-security-icon-group-24',
                            text: me.groupsBtnText,
                            scale: 'medium'
                        },
                        {
                            hidden: config.hideViewAwareComponents || false,
                            disabled: true,
                            selectionAware: true,
                            itemId: 'editPasswordBtn',
                            iconCls: 'modera-backend-security-icon-password-24',
                            text: me.changePasswordBtnText,
                            scale: 'medium'
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
        };

        me.config = Ext.apply(defaults, config || {});
        me.callParent([me.config]);

        me.addEvents(
            /**
             * @event newrecord
             * @param {Modera.backend.security.toolscontribution.view.user.List} me
             */
            'newrecord',
            /**
             * @event editrecord
             * @param {Modera.backend.security.toolscontribution.view.user.List} me
             * @param {Object} params
             */
            'editrecord',
            /**
             * @event editpassword
             * @param {Modera.backend.security.toolscontribution.view.user.List} me
             * @param {Object} params
             */
            'editpassword',
            /**
             * @event editgroups
             * @param {Modera.backend.security.toolscontribution.view.user.List} me
             * @param {Object} params
             */
            'editgroups'
        );

        me.assignListeners();
    },

    // private
    defaultRenderer: function(msg) {
        return function(value) {
            if (Ext.isEmpty(value)) {
                return '<span class="mfc-empty-text">' + (msg || '-') + '</span>';
            }

            return value;
        };
    },

    // private
    getSelectedRecord: function() {
        return this.getSelectedRecords()[0];
    },

    // private
    getSelectedRecords: function() {
        return this.getSelectionModel().getSelection();
    },

    // private
    getSelectedIds: function() {
        var records = this.getSelectedRecords();

        var ids = [];
        Ext.each(records, function(record) {
            ids.push(record.get('id'));
        });

        return ids;
    },

    // private
    assignListeners: function() {
        var me = this;

        me.down('#newRecordBtn').on('click', function() {
            me.fireEvent('newrecord', me);
        });

        me.on('selectionchange', function() {
            var btn = me.down('#editRecordBtn');
            if (me.getSelectedRecords().length > 1) {
                btn.btnEl.addCls('modera-backend-security-btn-disabled');
            } else {
                btn.btnEl.removeCls('modera-backend-security-btn-disabled');
            }
        });

        me.down('#editRecordBtn').on('click', function(btn) {
            var records = me.getSelectedRecords();
            if (records.length > 1) {
                btn.maybeShowMenu();
            } else {
                me.fireEvent('editrecord', me, { id: records[0].get('id') });
            }

        });

        me.down('#deleteBtn').on('click', function() {
            var ids = me.getSelectedIds();
            me.fireEvent('deleterecord', me, { id: ids.length > 1 ? ids : ids[0] });
        });

        me.down('#editPasswordBtn').on('click', function() {
            me.fireEvent('editpassword', me, { id: me.getSelectedRecord().get('id') });
        });

        me.down('#editGroupsBtn').on('click', function() {
            var ids = me.getSelectedIds();
            me.fireEvent('editgroups', me, { id: ids.length > 1 ? ids : ids[0] });
        });
    }
});