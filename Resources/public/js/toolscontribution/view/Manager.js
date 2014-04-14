/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.view.Manager', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.modera-backend-security-manager',

    requires: [
        'MFC.container.Header',
        'Modera.backend.security.toolscontribution.view.user.List',
        'Modera.backend.security.toolscontribution.view.group.Overview',
        'Modera.backend.security.toolscontribution.view.permission.List'
    ],

    // l10n
    headerTitleText: 'Security and permissions',
    btnUsersText: 'Users',
    btnGroupsText: 'Groups',
    btnPermissionsText: 'Permissions',

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
                            },
                            {
                                itemId: 'permissions',
                                pressed: config.sectionName == 'permissions',
                                text: me.btnPermissionsText,
                                iconCls: 'modera-backend-security-icon-permission-32'
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
                        },
                        {
                            itemId: 'permissions',
                            xtype: 'modera-backend-security-permission-list',
                            groupsStore: config['groupsStore'],
                            hasAccess: config.hasPermissionsAccess
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
    },

    /**
     * @param {String} sectionName
     * @param {Function} callback
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
    }
});