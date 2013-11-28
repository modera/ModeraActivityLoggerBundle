/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.module.toolscontribution.runtime.AvailableModulesListView', {
    extend: 'MF.viewsmanagement.views.AbstractView',

    requires: [
    ],

    // override
    getId: function() {
        return 'available-modules-list';
    },

    // override
    doCreateUi: function(params, onReadyCallback) {
        var me = this;

        var store = Ext.create('Ext.data.DirectStore', {
            fields: [
                'id', 'name'
            ],
            proxy: {
                type: 'direct',
                directFn: Actions.ModeraBackendModule_Default.getAvailableModules
            }
        });

        var panel = Ext.create('Ext.grid.Panel', {
            columns: [
                {
                    name: 'Name',
                    dataIndex: 'name',
                    flex: 1
                }
            ],
            store: store
        });

        store.load({
            callback: function() {
                onReadyCallback(panel);
            }
        });
    }
});