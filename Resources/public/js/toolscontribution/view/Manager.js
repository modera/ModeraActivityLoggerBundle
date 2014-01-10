/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.view.Manager', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.modera-backend-security-manager',

    requires: [
        'MFC.container.Header',
        'Modera.backend.security.toolscontribution.view.user.List',
        'Modera.backend.security.toolscontribution.view.group.Overview'
    ],

    // l10n
    headerTitleText: 'Security and permissions',
    btnUsersText: 'Users',
    btnGroupsText: 'Groups',

    // override
    constructor: function(config) {
        var me = this;

        var defaults = {
            basePanel: true,
            padding: 10,
            tbar: {
                xtype: 'mfc-header',
                title: me.headerTitleText,
                margin: '0 0 9 0',
                iconCls: 'modera-backend-security-tools-icon',
                closeBtn: true
            },
            layout: 'border',
            items: [
                {
                    region: 'west',
                    width: 200,
                    margin: '0 10 0 0',
                    items: {
                        xtype: 'buttongroup',
                        ui: 'menu',
                        cls: 'modera-backend-security-menu',
                        padding: 5,
                        layout: {
                            type: 'vbox',
                            align: 'stretch',
                            pack: 'start'
                        },
                        defaults: {
                            enableToggle: true,
                            allowDepress: false,
                            textAlign: 'left',
                            scale: 'large',
                            handler: function(btn) {
                                me.activateSection(btn.getItemId());
                                me.fireEvent('sectionchanged', me, btn.getItemId());
                            }
                        },
                        items: [
                            {
                                itemId: 'users',
                                pressed: config.sectionName == 'users',
                                text: me.btnUsersText,
                                iconCls: 'modera-backend-security-icon-user-32'
                            },
                            {
                                itemId: 'groups',
                                pressed: config.sectionName == 'groups',
                                text: me.btnGroupsText,
                                iconCls: 'modera-backend-security-icon-group-32'
                            }
                        ]
                    }
                },
                {
                    itemId: 'baseContainer',
                    region: 'center',
                    xtype: 'container',
                    layout: 'card',
                    activeItem: config.sectionName,
                    items: [
                        {
                            itemId: 'users',
                            xtype: 'modera-backend-security-user-list'
                        },
                        {
                            itemId: 'groups',
                            xtype: 'modera-backend-security-group-overview'
                        }
                    ]
                }
            ]
        };

        me.config = Ext.apply(defaults, config || {});
        me.callParent([me.config]);

        me.addEvents(
            /**
             * @event sectionchanged
             * @param {Modera.backend.security.toolscontribution.view.Manager} me
             * @param {String} id
             */
            'sectionchanged'
        );

        me.assignListeners();
    },

    /**
     * @param {String} sectionName
     */
    activateSection: function(sectionName, callback) {
        var me = this;

        var btnGroup = me.down('buttongroup');
        btnGroup.items.each(function(btn) {
            btn.toggle(sectionName == btn.getItemId());
        });
        me.down('#baseContainer').getLayout().setActiveItem(sectionName);

        if (callback) {
            callback();
        }
    },

    // private
    assignListeners: function() {
        var me = this;

        var usersList = me.down('modera-backend-security-user-list');
        usersList.on('newrecord', function(sourceComponent) {
            me.fireEvent('handleaction', 'newuser', sourceComponent);
        });
        usersList.on('editrecord', function(sourceComponent, params) {
            me.fireEvent('handleaction', 'edituser', sourceComponent, params);
        });
        usersList.on('deleterecord', function(sourceComponent, params) {
            me.fireEvent('handleaction', 'deleteuser', sourceComponent, params);
        });
        usersList.on('editpassword', function(sourceComponent, params) {
            me.fireEvent('handleaction', 'editpassword', sourceComponent, params);
        });
        usersList.on('editgroups', function(sourceComponent, params) {
            me.fireEvent('handleaction', 'editgroups', sourceComponent, params);
        });

        var groupsOverview = me.down('modera-backend-security-group-overview');
        groupsOverview.on('creategroup', function(sourceComponent) {
            me.fireEvent('handleaction', 'newgroup', sourceComponent);
        });
        groupsOverview.on('deletegroup', function(sourceComponent, record) {
            me.fireEvent('handleaction', 'deletegroup', sourceComponent, { id: record.get('id') });
        });
        groupsOverview.on('editgroup', function(sourceComponent, record) {
            me.fireEvent('handleaction', 'editgroup', sourceComponent, { id: record.get('id') });
        });
    }
});