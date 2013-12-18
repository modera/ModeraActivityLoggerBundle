/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.module.toolscontribution.view.InstalledModulesList', {
    extend: 'Modera.backend.module.toolscontribution.view.AbstractModulesList',
    alias: 'widget.modera-backend-module-installedmoduleslist',

    requires: [
        'Modera.backend.module.toolscontribution.store.InstalledModules',
        'MFC.container.Header'
    ],

    // l10n
    headerTitleText: 'Installed modules',
    showAvailableModulesText: 'Browse module market',
    emptyText: 'No modules currently installed.',

    // override
    constructor: function(config) {
        var me = this;
        var defaults = {
            emptyText: me.emptyText,
            store: Ext.create('Modera.backend.module.toolscontribution.store.InstalledModules'),
            tbar: {
                xtype: 'mfc-header',
                title: me.headerTitleText,
                margin: '0 0 9 0',
                closeBtn: true,
                items: [
                    '->',
                    {
                        itemId: 'showAvailableModules',
                        iconCls: 'modera-backend-module-icon market24',
                        text: me.showAvailableModulesText,
                        scale: 'medium'
                    }
                ]
            }
        };

        me.config = Ext.apply(defaults, config || {});
        me.callParent([me.config]);

        me.addEvents(
            /**
             * @event showmoduledetails
             * @param {Modera.backend.module.toolscontribution.view.InstalledModulesList} me
             * @param {Ext.data.Model} model
             */
            'showmoduledetails',
            /**
             * @event showavailablemodules
             * @param {Modera.backend.module.toolscontribution.view.InstalledModulesList} me
             */
            'showavailablemodules'
        );

        me.assignListeners();
    },

    // private
    assignListeners: function() {
        var me = this;

        me.on('itemclick', function(sm, record) {
            me.fireEvent('showmoduledetails', me, record);
        });

        me.down('#showAvailableModules').on('click', function() {
            me.fireEvent('showavailablemodules', me);
        });
    }
});