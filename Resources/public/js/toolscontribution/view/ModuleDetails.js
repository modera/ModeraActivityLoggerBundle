/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.module.toolscontribution.view.ModuleDetails', {
    extend: 'Ext.container.Container',
    alias: 'widget.modera-backend-module-moduledetails',

    requires: [
        'MFC.Date'
    ],

    // l10n
    versionFieldText: 'Version',
    createdAtFieldText: 'Last update',
    licenseFieldText: 'License',
    authorsFieldText: 'Authors',
    installedStatusText: 'Installed',
    currentVersionStatusText: 'Current version: {0}',
    installBtnText: 'Install the module',
    updateBtnText: 'Install update',
    removeBtnText: 'Uninstall',

    // override
    constructor: function(config) {
        var me = this;
        var defaults = {
            layout: 'border',
            cls: 'modera-backend-module-details',
            dto: {}
        };
        me.config = Ext.apply(defaults, config || {});

        me.config.items = [
            {
                xtype: 'container',
                region: 'west',
                width: 300,
                padding: '0 10 0 0',
                cls: 'info',
                items: [
                    {
                        xtype: 'form',
                        defaultType: 'displayfield',
                        margin: '10 0 10 0',
                        defaults: {
                            anchor: '100%',
                            margin: '0 0 0 0',
                            labelWidth: 120,
                            labelStyle: 'font-weight:normal',
                            labelAlign: 'right',
                            fieldStyle: 'font-size:inherit'
                        },
                        items: [
                            { name: 'lastVersion', fieldLabel: me.versionFieldText, value: me.config.dto.lastVersion },
                            { name: 'createdAt', fieldLabel: me.createdAtFieldText, value: MFC.Date.moment(me.config.dto.createdAt).fromNow() },
                            { name: 'license', fieldLabel: me.licenseFieldText, value: me.config.dto.license },
                            { name: 'authors', fieldLabel: me.authorsFieldText, value: me.config.dto.authors }
                        ]
                    },
                    {
                        xtype: 'container',
                        margin: '10 0 0 0',
                        layout: {
                            type: 'hbox',
                            pack: 'center'
                        },
                        hidden: (!me.config.dto.installed && !me.config.dto.updateAvailable),
                        items: [
                            {
                                hidden: (!me.config.dto.installed || me.config.dto.updateAvailable),
                                xtype: 'box',
                                cls: 'modera-backend-module-box-status mfc-box-status success',
                                html: me.installedStatusText
                            },
                            {
                                hidden: !me.config.dto.updateAvailable,
                                xtype: 'box',
                                cls: 'modera-backend-module-box-status mfc-box-status warning',
                                html: Ext.String.format(me.currentVersionStatusText, me.config.dto.currentVersion)
                            }
                        ]
                    },
                    {
                        xtype: 'buttongroup',
                        layout: {
                            type: 'hbox',
                            pack: 'center'
                        },
                        defaults: {
                            scale: 'medium'
                        },
                        margin: '10 0 0 0',
                        items: [
                            {
                                itemId: 'requireBtn',
                                hidden: (me.config.dto.installed && !me.config.dto.updateAvailable),
                                xtype: 'button',
                                text: (me.config.dto.updateAvailable ? me.updateBtnText : me.installBtnText)
                            },
                            {
                                itemId: 'removeBtn',
                                hidden: !me.config.dto.installed,
                                xtype: 'button',
                                text: me.removeBtnText
                            }
                        ]
                    }
                ]
            },
            {
                region: 'center',
                frame: true,
                layout: 'fit',
                xtype: 'tabpanel',
                defaults: {
                    autoScroll: true
                },
                items: [
                    {
                        title: 'Overview',
                        bodyPadding: 20,
                        items: [
                            {
                                cls: 'description',
                                html: me.config.dto.description
                            }
                        ]
                    },
                    {
                        title: 'Update history'
                    }
                ]
            }
        ];

        me.callParent([me.config]);

        me.addEvents(
            /**
             * @event requiremodule
             * @param {Modera.backend.module.toolscontribution.view.ModuleDetails} me
             * @param {Object} dto
             */
            'requiremodule',
            /**
             * @event removemodule
             * @param {Modera.backend.module.toolscontribution.view.ModuleDetails} me
             * @param {Object} dto
             */
            'removemodule'
        );

        me.assignListeners();
    },

    // private
    assignListeners: function() {
        var me = this;

        me.down('#requireBtn').on('click', function() {
            me.fireEvent('requiremodule', me, me.config.dto);
        });

        me.down('#removeBtn').on('click', function() {
            me.fireEvent('removemodule', me, me.config.dto);
        });
    }
});