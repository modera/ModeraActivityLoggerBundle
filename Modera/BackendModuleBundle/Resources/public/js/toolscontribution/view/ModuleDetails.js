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
    dependencyStatusText: 'Dependency',
    currentVersionStatusText: 'Current version: {0}',
    installBtnText: 'Install the module',
    updateBtnText: 'Install update',
    removeBtnText: 'Uninstall',
    overviewTabText: 'Overview',
    screenshotsTabText: 'Screenshots',

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
                cls: 'info mfc-dashed-top-line',
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
                                hidden: (!me.config.dto.installed || me.config.dto.updateAvailable || me.config.dto.isDependency),
                                xtype: 'box',
                                cls: 'modera-backend-module-box-status mfc-box-status success',
                                html: me.installedStatusText
                            },
                            {
                                hidden: !me.config.dto.isDependency,
                                xtype: 'box',
                                cls: 'modera-backend-module-box-status mfc-box-status warning',
                                html: me.dependencyStatusText
                            },
                            {
                                hidden: me.config.dto.isDependency || !me.config.dto.updateAvailable,
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
                                iconCls: 'modera-backend-module-icon ' + (me.config.dto.updateAvailable ? 'update24' : 'install24'),
                                hidden: (me.config.dto.installed && !me.config.dto.updateAvailable && !me.config.dto.isDependency),
                                xtype: 'button',
                                text: (me.config.dto.updateAvailable ? me.updateBtnText : me.installBtnText)
                            },
                            {
                                itemId: 'removeBtn',
                                iconCls: 'modera-backend-module-icon remove24',
                                hidden: !me.config.dto.installed || me.config.dto.isDependency,
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
                cls: 'rounded-top',
                defaults: {
                    autoScroll: true
                },
                items: [
                    {
                        title: me.overviewTabText,
                        bodyPadding: 0,
                        items: [
                            {
                                cls: 'description',
                                bodyPadding: 20,
                                html: me.config.dto.description
                            },
                            {
                                xtype: 'container',
                                hidden: (me.config.dto.screenshots.length ? false : true),
                                items: {
                                    itemId: 'thumbnails',
                                    xtype: 'dataview',
                                    multiSelect: false,
                                    singleSelect: true,
                                    autoScroll: false,
                                    cls: 'thumbnails',
                                    store: Ext.create('Ext.data.Store', {
                                        autoDestroy: true,
                                        idIndex: 0,
                                        fields: [
                                            'thumbnail', 'src'
                                        ],
                                        data: me.config.dto.screenshots.slice(0, 3)
                                    }),
                                    border: 0,
                                    tpl: new Ext.XTemplate(
                                        '<ul>',
                                            '<tpl for=".">',
                                                '<li>',
                                                    '<span>',
                                                        '<img src="{thumbnail}" />',
                                                    '</span>',
                                                '</li>',
                                            '</tpl>',
                                        '</ul>',
                                        '<div class="more">',
                                            '<div id="moreButton">...</div>',
                                        '</div>'
                                    ),
                                    itemSelector: 'li'
                                }
                            },
                            {
                                cls: 'longDescription',
                                bodyPadding: 20,
                                html: me.config.dto.longDescription
                            }
                        ]
                    },
                    {
                        itemId: 'screenshotsTab',
                        hidden: (me.config.dto.screenshots.length ? false : true),
                        title: me.screenshotsTabText,
                        items: {
                            itemId: 'screenshots',
                            xtype: 'dataview',
                            cls: 'screenshots',
                            store: Ext.create('Ext.data.Store', {
                                autoDestroy: true,
                                idIndex: 0,
                                fields: [
                                    'src'
                                ],
                                data: me.config.dto.screenshots
                            }),
                            border: 0,
                            tpl: new Ext.XTemplate(
                                '<ul>',
                                    '<tpl for=".">',
                                        '<li>',
                                            '<span>',
                                                '<img src="{src}" />',
                                            '</span>',
                                        '</li>',
                                    '</tpl>',
                                '</ul>'
                            ),
                            itemSelector: 'li'
                        }
                    }/*,
                    {
                        title: 'Update history'
                    }*/
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

        me.down('#thumbnails').on('selectionchange', function() {
            var selection = this.getSelectionModel().getSelection();
            if (selection.length > 0) {
                me.activateScreenshotTab(selection[0].get('src'));
            }
        });

        me.down('#thumbnails').on('containerclick', function(dataView, e) {
            var target = e.getTarget();
            if (target.id == 'moreButton') {
                me.activateScreenshotTab();
            }
        });
    },

    // private
    activateScreenshotTab: function(screenshot) {
        var me = this;
        var tabPanel = me.down('tabpanel');
        tabPanel.setActiveTab('screenshotsTab');
        if (screenshot) {
            // TODO: scrolling
        }
    }
});