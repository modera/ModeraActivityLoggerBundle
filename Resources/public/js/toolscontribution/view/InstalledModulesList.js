/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.module.toolscontribution.view.InstalledModulesList', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.modera-backend-module-installedmoduleslist',

    // override
    constructor: function(config) {
        // TODO temporary
        var store = Ext.create('Ext.data.DirectStore', {
            fields: [
                'id', 'name'
            ],
            proxy: {
                type: 'direct',
                directFn: Actions.ModeraBackendModule_Default.getInstalledModules
            }
        });

        var defaults = {
            columns: [
                {
                    name: 'Name',
                    dataIndex: 'name',
                    flex: 1
                }
            ],
            store: store,
            tbar: [
                '->',
                {
                    itemId: 'showAvailableModules',
                    text: 'Browse module market'
                }
            ]
        };

        this.config = Ext.apply(defaults, config || {});
        this.callParent([this.config]);

        this.addEvents(
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

        this.assignListeners();
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