/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.translationstool.toolscontribution.view.List', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.modera-backend-translations-tool-list',

    requires: [
        'MFC.container.Header',
        'MFC.container.Message',
        //'MFC.HasSelectionAwareComponentsPlugin',
        'MFC.form.field.plugin.FieldInputFinishedPlugin',
        'Modera.backend.translationstool.toolscontribution.store.Translations'
    ],

    //plugins: [Ext.create('MFC.HasSelectionAwareComponentsPlugin')],

    // l10n
    titleText: 'Translations',
    emptyListText: 'No items found',
    bundleNameColumnText: 'Bundle name',
    tokenNameColumnText: 'Token name',
    importBtnText: 'Import',
    compileBtnText: 'Compile...',
    deleteBtnText: 'Delete selected',
    translationsWereChangedText: 'Your translations were changed. Click compile button to make language files up-to-date.',
    filterPlaceholderText: 'type here to filter...',

    // override
    constructor: function(config) {
        var me = this;

        var columns = [];
        var languagesStore = config['languagesStore'];
        languagesStore.each(function(language) {
            columns.push({
                languageId: language.get('id'),
                text: language.get('name'),
                dataIndex: 'translations',
                flex: 3,
                renderer: function(values, metaData, record) {
                    if (values[language.get('id')]) {
                        if (values[language.get('id')]['isNew']) {
                            metaData.tdCls += 'new';
                        }

                        return values[language.get('id')]['translation'];
                    } else {
                        return '';
                    }
                }
            });
        });

        var parts = config['activeFilter'].split('-');
        var filterId = parts.shift();
        var filterValue = parts.join('-');

        var toolbarItems = [];
        if (config['filters'].length) {
            toolbarItems.push({
                xtype: 'box',
                html: 'Show:'
            });
            Ext.each(config['filters'], function(filter, index) {
                toolbarItems.push({
                    itemId: filter.id,
                    text: filter.name,
                    pressed: filter.id === filterId,
                    allowDepress: false,
                    scale: 'medium',
                    toggleGroup: 'show'
                });
            });

            toolbarItems.push({
                itemId: 'filter',
                xtype: 'textfield',
                emptyText: this.filterPlaceholderText,
                plugins: [Ext.create('MFC.form.field.plugin.FieldInputFinishedPlugin', {
                    timeout: 800
                })],
                enableKeyEvents: true,
                height: 30,
                value: filterValue,
                tid: 'filterInput',
            });
        }

        var store = Ext.create('Modera.backend.translationstool.toolscontribution.store.Translations');

        var defaults = {
            cls: 'modera-backend-translations-tool',
            monitorModel: ['modera.translations_bundle.translation_token', 'modera.translations_bundle.language_translation_token'],
            basePanel: true,
            padding: 10,
            border: true,
            selType: 'cellmodel',
            viewConfig: {
                stripeRows: false,
                getRowClass: function(record, rowIndex, rowParams, store) {
                    if (record.get('isObsolete')) {
                        return 'obsolete';
                    } else {
                        return '';
                    }
                }
            },
            columns: [
                me.getCheckerColumnConfig(),
                {
                    text: me.bundleNameColumnText,
                    dataIndex: 'bundleName',
                    flex: 1,
                    renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                        var baseCSSPrefix = Ext.baseCSSPrefix;
                        metaData.tdCls = baseCSSPrefix + 'grid-cell-special';
                        return value;
                    }
                },
                {
                    text: me.tokenNameColumnText,
                    dataIndex: 'tokenName',
                    flex: 2,
                    renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                        var baseCSSPrefix = Ext.baseCSSPrefix;
                        metaData.tdCls = baseCSSPrefix + 'grid-cell-special';
                        return value;
                    }
                }
            ].concat(columns),
            store: store,
            emptyText: me.emptyListText,
            emptyCls: 'mfc-grid-empty-text',
            listeners: {
                'afterrender': function(grid) {
                    grid.view.refresh();
                }
            },
            dockedItems: [
                {
                    dock: 'top',
                    itemId: 'header',
                    xtype: 'mfc-header',
                    iconCls: 'modera-backend-translations-tool-tools-icon',
                    title: me.titleText,
                    margin: '0 0 9 0',
                    items: [
                        '->'
                        /*{
                            itemId: 'applications',
                            xtype: 'combo',
                            editable: false,
                            store: Ext.create('Ext.data.Store', {
                                fields: ['id', 'name'],
                                data : []
                            }),
                            width:320,
                            queryMode: 'local',
                            displayField: 'name',
                            valueField: 'id'
                        },
                        {
                            glyph: FontAwesome.resolve('wrench'),
                            text: 'Settings...',
                            scale: 'medium'
                        },*/
                    ]
                },
                {
                    dock: 'top',
                    hidden: true,
                    itemId: 'messages-box',
                    xtype: 'mfc-header',
                    type: 'notification',
                    margin: '0 0 10 0',
                    items: [
                        {
                            xtype: 'mfc-message',
                            type: 'warning',
                            text: me.translationsWereChangedText,
                            items: [
                                {
                                    itemId: 'compile',
                                    text: me.compileBtnText
                                }
                            ],
                            tid: 'translationsChangedNotification'
                        },
                        '->'
                    ]
                },
                {
                    dock: 'top',
                    xtype: 'toolbar',
                    //padding: 2,
                    items: [
                        {
                            itemId: 'import',
                            iconCls: 'icon-import-24',
                            text: me.importBtnText,
                            scale: 'medium',
                            tid: 'importBtn'
                        },
                        {
                            itemId: 'delete',
                            iconCls: 'mfc-icon-delete-24',
                            text: me.deleteBtnText,
                            scale: 'medium',
                            //selectionAware: true,
                            //multipleSelectionSupported:true,
                            disabled: true
                        },
                        '->'
                    ].concat(toolbarItems)
                },
                {
                    dock: 'bottom',
                    xtype: 'pagingtoolbar',
                    //padding: 2,
                    store: store,
                    displayInfo: true
                }
            ]
        };
        me.config = Ext.apply(defaults, config);
        me.callParent([me.config]);

        me.addEvents(
            /**
             * @event filterchanged
             * @param {Modera.backend.carstock.cars.view.List} me
             * @param {Object} params
             */
            'filterchanged',

            /**
             * @event import
             * @param {Modera.backend.translationstool.toolscontribution.view.List} me
             */
            'import',

            /**
             * @event compile
             * @param {Modera.backend.translationstool.toolscontribution.view.List} me
             */
            'compile',

            /**
             * @event delete
             * @param {Modera.backend.translationstool.toolscontribution.view.List} me
             * @param {Ext.data.Model[]} models
             */
            'delete',

            /**
             * @event edittranslation
             * @param {Modera.backend.translationstool.toolscontribution.view.List} me
             * @param {Object} params
             */
            'edittranslation'
        );

        me.assignListeners();
    },

    // public
    setCompileVisible: function(value) {
        var me = this;
        me.down('#messages-box').setVisible(value);
    },

    // public
    selectCell: function(pos) {
        var me = this;
        if (pos) {
            me.getSelectionModel().setCurrentPosition(pos);
        }

        if (me.filterFocus) {
            setTimeout(function() {
                me.down('#filter').focus();
                me.filterFocus = false;
            }, 200);
        }
    },

    // public
    getSelectedCell: function() {
        var me = this;

        if (me.prevPosition) {
            return me.prevPosition;
        }

        return null;
    },

    // public
    findFirstEditableCell: function() {
        var me = this;
        var pos = null;

        if (me.getStore().getCount()) {
            Ext.each(me.columns, function(column, index) {
                if (column['languageId']) {
                    pos = { row: 0, column: index };
                    return false;
                }
            });
        }

        return pos;
    },

    // public
    findCellByTranslationId: function(id) {
        var me = this;
        var pos = null;

        me.getStore().each(function(record, index) {
            Ext.Object.each(record.get('translations'), function(languageId, translation) {
                if (id == translation['id']) {
                    Ext.each(me.columns, function(column, index2) {
                        if (column['languageId'] && column['languageId'] == languageId) {
                            pos = { row: index, column: index2 };
                            return false;
                        }
                    });
                    return false;
                }
            });
        });

        return pos;
    },

    // private
    assignListeners: function() {
        var me = this;

        me.prevPosition = null;
        me.filterFocus = false;

        me.on('headerclick', me.onHeaderClick);

        Ext.each(me.query('button[toggleGroup=show]'), function(item) {
            item.on('click', function(btn) {
                var id = btn.getItemId();
                if (me.down('#filter').getValue().length) {
                    id += '-' + me.down('#filter').getValue();
                }
                me.fireEvent('filterchanged', me, { id: id });
                me.prevPosition = null;
            });
        });


        me.down('#filter').on('inputfinished', function(field, e) {
            var id = me.down('button[toggleGroup=show][pressed=true]').getItemId();
            if (field.getValue().length) {
                id += '-' + field.getValue();
            }
            me.fireEvent('filterchanged', me, { id: id });
            me.prevPosition = null;
            me.filterFocus = true;
        });

        me.down('#import').on('click', function(btn) {
            me.fireEvent('import', me);
        });

        me.down('#compile').on('click', function(btn) {
            me.fireEvent('compile', me);
        });

        me.down('#delete').on('click', function(btn) {
            var models = me.getCheckedRecords();
            me.fireEvent('delete', me, models);
        });

        me.on('beforeselect', function(sm, record, index) {
            var position = sm.getCurrentPosition();
            var column = me.columns[position.column];

            if (column) {
                if (column['languageId']) {
                    me.prevPosition = position;
                } else {
                    if (me.prevPosition) {
                        me.selectCell(me.prevPosition);
                    }
                    return false;
                }
            }
        });

        me.on('afterlayout', function(grid, layout) {
            var checkedHd = false;
            Ext.each(me.columns, function(column, index) {
                if (column['isCheckerHd']) {
                    var view = me.getView();
                    me.getStore().each(function(record, index) {
                        var node = view.getNode(record);
                        if (Ext.fly(node).hasCls(Ext.baseCSSPrefix + 'grid-row-checked')) {
                            checkedHd = true;
                            return false;
                        }
                    });

                    var checkerOnCls = Ext.baseCSSPrefix + 'grid-hd-checker-on';
                    if (checkedHd) {
                        column.el.addCls(checkerOnCls);
                    } else {
                        column.el.removeCls(checkerOnCls);
                    }
                    me.down('#delete').setDisabled(!checkedHd);

                    return false;
                }
            });
        });

        me.on('cellclick', function(view, td, cellIndex, record, tr, rowIndex, e) {
            var column = me.columns[cellIndex];
            if (column['isCheckerHd'] || !column['languageId']) {
                me.toggleChecker(tr, column);
            }
        });

        var edit = function(veiw, td, cellIndex, record, tr, rowIndex, e) {
            var column = me.columns[cellIndex];
            if (column['languageId']) {
                var translation = record.get('translations')[column['languageId']];
                me.fireEvent('edittranslation', me, { id: translation['id'] });
            }
        };
        me.on('celldblclick', edit);
        me.on('cellkeydown', function(veiw, td, cellIndex, record, tr, rowIndex, e) {
            if (e.browserEvent.keyCode == e.RETURN) {
                edit(veiw, td, cellIndex, record, tr, rowIndex, e);
            }
        });
    },

    // private
    getCheckerColumnConfig: function() {
        return {
            isCheckerHd: true,
            text : '&#160;',
            clickTargetName: 'el',
            width: 24,
            sortable: false,
            draggable: false,
            resizable: false,
            hideable: false,
            menuDisabled: true,
            dataIndex: '',
            cls: Ext.baseCSSPrefix + 'column-header-checkbox ',
            renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                var baseCSSPrefix = Ext.baseCSSPrefix;
                metaData.tdCls = baseCSSPrefix + 'grid-cell-special ' + baseCSSPrefix + 'grid-cell-row-checker';
                return '<div class="' + baseCSSPrefix + 'grid-row-checker">&#160;</div>';
            },
            locked: false
        };
    },

    // private
    getCheckedRecords: function() {
        var me = this;
        var records = [];
        var view = me.getView();
        me.getStore().each(function(record, index) {
            var node = view.getNode(record);
            if (Ext.fly(node).hasCls(Ext.baseCSSPrefix + 'grid-row-checked')) {
                records.push(record);
            }
        });

        return records;
    },

    // private
    checkAll: function() {
        var me = this;
        var view = me.getView();
        me.getStore().each(function(record, index) {
            var node = view.getNode(record);
            if (!Ext.fly(node).hasCls(Ext.baseCSSPrefix + 'grid-row-checked')) {
                Ext.fly(node).addCls(Ext.baseCSSPrefix + 'grid-row-checked');
            }
        });
    },

    // private
    uncheckAll: function() {
        var me = this;
        var view = me.getView();
        me.getStore().each(function(record, index) {
            var node = view.getNode(record);
            if (Ext.fly(node).hasCls(Ext.baseCSSPrefix + 'grid-row-checked')) {
                Ext.fly(node).removeCls(Ext.baseCSSPrefix + 'grid-row-checked');
            }
        });
    },

    // private
    toggleChecker: function(node, header) {
        var me = this;
        var checkedHd = false;
        var view = me.getView();

        if (Ext.fly(node).hasCls(Ext.baseCSSPrefix + 'grid-row-checked')) {
            Ext.fly(node).removeCls(Ext.baseCSSPrefix + 'grid-row-checked');
        } else {
            checkedHd = true;
            Ext.fly(node).addCls(Ext.baseCSSPrefix + 'grid-row-checked');
        }

        if (!checkedHd) {
            me.getStore().each(function(record, index) {
                var node = view.getNode(record);
                if (Ext.fly(node).hasCls(Ext.baseCSSPrefix + 'grid-row-checked')) {
                    checkedHd = true;
                    return false;
                }
            });
        }

        var checkerOnCls = Ext.baseCSSPrefix + 'grid-hd-checker-on';
        if (checkedHd) {
            header.el.addCls(checkerOnCls);
        } else {
            header.el.removeCls(checkerOnCls);
        }
        me.down('#delete').setDisabled(!checkedHd);
    },

    // private
    onHeaderClick: function(headerCt, header, e) {
        var me = this;
        var checkerOnCls = Ext.baseCSSPrefix + 'grid-hd-checker-on';

        if (header.isCheckerHd) {
            e.stopEvent();

            var isChecked = header.el.hasCls(checkerOnCls);
            if (isChecked) {
                me.uncheckAll();
                header.el.removeCls(checkerOnCls);
            } else {
                me.checkAll();
                header.el.addCls(checkerOnCls);
            }
            me.down('#delete').setDisabled(isChecked);
        }
    }
});