/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.module.toolscontribution.view.AvailableModulesList', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.modera-backend-module-availablemoduleslist',

    requires: [
        'Modera.backend.module.toolscontribution.store.AvailableModules'
    ],

    // l10n
    showInstalledModulesText: 'Back',

    // override
    constructor: function(config) {
        var me = this;
        var defaults = {
            hideHeaders: true,
            columns: [
                {
                    dataIndex: 'name',
                    flex: 2
                },
                {
                    dataIndex: 'description',
                    flex: 1
                },
                {
                    dataIndex: 'license'
                },
                {
                    dataIndex: 'lastVersion',
                    renderer: function(lastVersion, p, record) {
                        var resp = lastVersion;
                        if (record.get('currentVersion') && lastVersion !== record.get('currentVersion')) {
                            resp += ' <i>(' + record.get('currentVersion') + ')</i>'
                        }

                        return resp;
                    }
                }
            ],
            store: Ext.create('Modera.backend.module.toolscontribution.store.AvailableModules'),
            tbar: [
                '->',
                {
                    itemId: 'showInstalledModules',
                    text: me.showInstalledModulesText
                }
            ]
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

        this.assignListeners();
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