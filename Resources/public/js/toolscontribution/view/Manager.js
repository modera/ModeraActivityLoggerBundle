/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.view.Manager', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.modera-backend-security-manager',

    requires: [
        'MFC.container.Header'
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
                    defaults: {
                        border: false,
                        layout: 'fit'
                    }
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

        var baseContainer = me.down('#baseContainer');
        var oldActivityContainer = baseContainer.getLayout().getActiveItem(),
            newActivityContainer = baseContainer.down(Ext.String.format('component[activity={0}]', sectionName));

        baseContainer.getLayout().setActiveItem(newActivityContainer);
        me.fireEvent('activitychange', me, newActivityContainer, oldActivityContainer);

        if (callback) {
            callback();
        }
    },

    /**
     * @param sections
     */
    addSections: function(sections) {
        var me = this;

        Ext.each(sections, function(sectionConfig) {
            me.down('#baseContainer').add({
                xtype: 'container',
                activity: sectionConfig.name,
                reconfigureOnActivate: sectionConfig.reconfigureOnActivate || false
            })
        });
    }
});