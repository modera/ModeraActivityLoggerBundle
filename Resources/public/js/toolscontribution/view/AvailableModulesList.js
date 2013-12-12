/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.module.toolscontribution.view.AvailableModulesList', {
    extend: 'Modera.backend.module.toolscontribution.view.AbstractModulesList',
    alias: 'widget.modera-backend-module-availablemoduleslist',

    requires: [
        'Modera.backend.module.toolscontribution.store.AvailableModules',
        'MFC.container.Header'
    ],

    // l10n
    headerTitleText: 'Module market',
    showInstalledModulesText: 'Back',
    emptyText: 'No available modules.',

    // override
    constructor: function(config) {
        var me = this;
        var defaults = {
            emptyText: me.emptyText,
            store: Ext.create('Modera.backend.module.toolscontribution.store.AvailableModules'),
            tbar: {
                xtype: 'mfc-header',
                title: me.headerTitleText,
                margin: '0 0 9 0',
                items: [
//                    {
//                        text: 'Refresh',
//                        scale: 'medium'
//                    },
                    '->',
                    {
                        itemId: 'showInstalledModules',
                        text: me.showInstalledModulesText,
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
             * @param {Modera.backend.module.toolscontribution.view.AvailableModulesList} me
             * @param {Ext.data.Model} model
             */
            'showmoduledetails',
            /**
             * @event showinstalledmodules
             * @param {Modera.backend.module.toolscontribution.view.AvailableModulesList} me
             */
            'showinstalledmodules'
        );

        me.assignListeners();
    },

    // private
    assignListeners: function() {
        var me = this;

        me.on('itemclick', function(sm, record) {
            me.fireEvent('showmoduledetails', me, record);
        });

        me.down('#showInstalledModules').on('click', function() {
            me.fireEvent('showinstalledmodules', me);
        });
    }
});