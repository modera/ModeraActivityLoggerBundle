/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.configutils.view.PropertiesGrid', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.modera-configutils-propertiesgrid',

    requires: [
        'MF.Util',
        'Ext.grid.plugin.CellEditing',
        'Modera.backend.configutils.view.WidgetsPoolEditorColumn',
        'Modera.backend.configutils.store.Properties'
    ],

    /**
     * @cfg {Object} editorsPool
     */

    /**
     * @param {Object} config
     */
    constructor: function(config) {
        MF.Util.validateRequiredConfigParams(this, config, ['editorsPool']);

        if (!config['store']) {
            config.store = Ext.create('Modera.backend.configutils.store.Properties');
        }

        var defaults = {
            border: true,
            hideHeaders: true,
            plugins: [
                Ext.create('Ext.grid.plugin.CellEditing', {
                    clicksToEdit: 1
                })
            ],
            columns: [
                {
                    dataIndex: 'readableName',
                    flex: 1
                },
                {
                    xtype: 'modera-configutils-widgetspooleditorcolumn',
                    dataIndex: 'value',
                    flex: 1,
                    editorsPool: config.editorsPool
                }
            ]
        };

        this.config = Ext.apply(defaults, config);
        this.callParent([this.config]);

        this.assignListeners();
    },

    // private
    assignListeners: function() {
        this.on('edit', function(editor, e) {
            var r = e.record;

            Actions.ModeraBackendConfigUtils_Default.update({
                record: {
                    id: r.get('id'),
                    value: r.get('value')
                }
            })
        });
    }
});