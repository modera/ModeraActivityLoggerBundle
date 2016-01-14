/**
 * Allows to create a grid where property values can be edited.
 *
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
     * Optional category name you want to have configuration properties loaded from.
     *
     * @cfg {String} category
     */
    /**
     * Optional.
     *
     * Column editors definition. Sample:
     *
     *     {
     *         // configuration property name : widget definition
     *         description: { xtype: 'textfield },
     *         isEnabled: {
     *             xtype: 'combo',
     *             store: [[true, 'Yes'], [false, 'No']]
     *         },
     *         startAt: Ext.create('Ext.form.field.Time', {
     *             increment: 5
     *         })
     *     }
     *
     * @cfg {Object} editorsPool
     */
    /**
     * If not editor is specified in `editorsPool` we can use a default editor.
     *
     * @cfg {Object} defaultEditor
     */
    /**
     * An optional function that if provided will be responsible for rendering value for "value" column. When
     * invoked function will received these parameters:
     *
     *  * value that column is bound to
     *  * metadata
     *  * record
     *
     * @cfg {Function} columnRenderer
     */

    /**
     * @param {Object} config
     */
    constructor: function(config) {
        config = config || {};
        config.store = Ext.create('Modera.backend.configutils.store.Properties');

        var me = this;

        var defaults = {
            border: true,
            hideHeaders: true,
            plugins: [
                Ext.create('MFC.grid.plugin.VolatileCellEditing', {
                    pluginId: 'editor',
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
                    editorsFactory: Ext.bind(me.editorsFactory, me),
                    renderer: function(v, md, record) {
                        if (Ext.isFunction(config.columnRenderer)) {
                            return config.columnRenderer(v, md, record);
                        }

                        return record.get('readableValue');
                    }
                }
            ]
        };

        this.config = Ext.apply(defaults, config);
        this.callParent([this.config]);

        this.assignListeners();
        this.applyFiltering(config.store, config);
    },

    // private
    applyFiltering: function(store, config) {
        var filters = [
            { property: 'isExposed', value: 'eq:true' }
        ];

        if (config && config.category) {
            filters.push({
                property: 'category',
                value: 'eq:' + config.category
            });
        }

        store.filter(filters);
    },

    // private
    editorsFactory: function(record) {
        var me = this;

        if (record.get('isReadOnly')) {
            return false;
        }

        var pool = me.config.editorsPool || {},
            name = record.get('name');

        if (pool[name]) {
            var editor = pool[name];

            if (Ext.isFunction(editor)) {
                return editor(record);
            } else { // widget definition or widget instance
                return editor;
            }
        } else {
            var editorConfig = record.get('editorConfig');
            if (null !== editorConfig && Ext.isObject(editorConfig)) {
                var widget = me.createValueEditor(editorConfig);
                if (null !== widget) {
                    return widget;
                }
            }

            if (me.config.defaultEditor) {
                return me.config.defaultEditor;
            } else {
                return {
                    xtype: 'textfield'
                };
            }

        }
    },

    /**
     * You can override this method if you need a more sophisticated logic of column editor creation.
     *
     * These returned values can be returned by this method:
     *
     *  * null -- method can't create a widget, letting use a `defaultEditor` ( if any )
     *  * false -- the column must not be editable
     *  * object -- widget definition to use to edit value of this column
     *
     * @protected
     * @param {Object} editorConfig
     * @param {Ext.data.Model} record
     *
     * @return {null|false|Object}
     */
    createValueEditor: function(editorConfig, record) {
        if (editorConfig['className']) {
            return Ext.create(editorConfig.className, editorConfig);
        } else {
            return editorConfig;
        }
    },

    // private
    assignListeners: function() {
        var me = this;

        this.on('edit', function(editor, e) {
            var r = e.record,
                oldValue = e.originalValue,
                newValue = r.get('value');

            if (oldValue != newValue) {
                r.set('readableValue', '');

                var params = {
                    record: {
                        id: r.get('id'),
                        value: newValue
                    }
                };
                Actions.ModeraBackendConfigUtils_Default.update(params);
            }
        });

        this.on('itemclick', function(cmp, record) {
            me.getPlugin('editor').startEdit(record, 1);
        });
    }
});