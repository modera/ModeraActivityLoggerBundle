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
    groupsColumnHeaderText: 'Membership',
    addBtnText: 'New user',
    editBtnText: 'Edit selected',
    groupsBtnText: 'Group membership...',
    changePasswordBtnText: 'Change password',
    deleteBtnText: 'Delete',

    // override
    constructor: function(config) {
        var me = this;

        var store = Ext.create('Modera.backend.security.toolscontribution.store.Users');

        var defaults = {
            rounded: true,
            border: true,
            monitorModel: 'modera.security_bundle.user',
            emptyCls: 'mfc-grid-empty-text',
            store: store,
            columns: [
                {
                    width: 250,
                    text: me.firstNameColumnHeaderText,
                    dataIndex: 'firstName',
                    renderer: me.defaultRenderer()
                },
                {
                    width: 250,
                    text: me.lastNameColumnHeaderText,
                    dataIndex: 'lastName',
                    renderer: me.defaultRenderer()
                },
                {
                    width: 250,
                    text: me.usernameColumnHeaderText,
                    dataIndex: 'username',
                    renderer: me.defaultRenderer()
                },
                {
                    width: 250,
                    text: me.emailColumnHeaderText,
                    dataIndex: 'email',
                    renderer: me.defaultRenderer()
                },
                {
                    flex: 1,
                    text: me.groupsColumnHeaderText,
                    dataIndex: 'groups',
                    renderer: me.defaultRenderer()
                }
            ],
            dockedItems: [
                {
                    xtype: 'toolbar',
                    dock: 'top',
                    items: [
                        {
                            itemId: 'newRecordBtn',
                            iconCls: 'mfc-icon-add-24',
                            text: me.addBtnText,
                            scale: 'medium'
                        },
                        '->',
                        {
                            xtype: 'splitbutton',
                            disabled: true,
                            selectionAware: true,
                            itemId: 'editRecordBtn',
                            iconCls: 'mfc-icon-edit-24',
                            text: me.editBtnText,
                            scale: 'medium',
                            menu: Ext.create('Ext.menu.Menu', {
                                items: [
                                    {
                                        itemId: 'deleteBtn',
                                        text: this.deleteBtnText,
                                        scale: 'medium',
                                        iconCls: 'mfc-icon-delete-24'
                                    }
                                ]
                            })
                        },
                        {
                            disabled: true,
                            selectionAware: true,
                            itemId: 'editGroupsBtn',
                            iconCls: 'modera-backend-security-icon-group-24',
                            text:me.groupsBtnText,
                            scale: 'medium'
                        },
                        {
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
             * @param {Modera.backend.security.toolscontribution.view.UsersList} me
             * @param {Object} params
             */
            'editrecord',
            /**
             * @event editpassword
             * @param {Modera.backend.security.toolscontribution.view.UsersList} me
             * @param {Object} params
             */
            'editpassword',
            /**
             * @event editgroups
             * @param {Modera.backend.security.toolscontribution.view.UsersList} me
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
        return this.getSelectionModel().getSelection()[0];
    },

    // private
    assignListeners: function() {
        var me = this;

        me.down('#newRecordBtn').on('click', function() {
            me.fireEvent('newrecord', me);
        });

        me.down('#editRecordBtn').on('click', function() {
            me.fireEvent('editrecord', me, { id: me.getSelectedRecord().get('id') });
        });

        me.down('#deleteBtn').on('click', function() {
            me.fireEvent('deleterecord', me, { id: me.getSelectedRecord().get('id') });
        });

        me.down('#editPasswordBtn').on('click', function() {
            me.fireEvent('editpassword', me, { id: me.getSelectedRecord().get('id') });
        });

        me.down('#editGroupsBtn').on('click', function() {
            me.fireEvent('editgroups', me, { id: me.getSelectedRecord().get('id') });
        });
    }
});