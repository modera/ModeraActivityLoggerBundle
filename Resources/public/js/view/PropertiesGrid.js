/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.configutils.view.PropertiesGrid', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.modera-configutils-propertiesgrid',

    requires: [
        'MF.Util',
        'Ext.grid.plugin.CellEditing',
        'MFC.grid.column.WidgetsPoolEditorColumn',
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
                Ext.create('MFC.grid.plugin.VolatileCellEditing', {
                    clicksToEdit: 1
                })
            ],
            columns: [
                {
                    dataIndex: 'readableName',
                    flex: 1
                },
                {
                    xtype: 'mfc-editorcolumn',
                    dataIndex: 'value',
                    flex: 1,
                    editorsFactory: function(record) {
                        if (record.get('isReadOnly')) {
                            return false;
                        }

                        var pool = config.editorsPool,
                            name = record.get('name');

                        if (pool[name]) {
                            var editor = pool[name];

                            if (Ext.isFunction(editor)) {
                                return editor(record);
                            } else { // widget definition or widget instance
                                return editor;
                            }
                        }

                        return false;
                    },
                    renderer: function(v, md, record) {
                        return record.get('readableValue');
                    }
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