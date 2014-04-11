/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.translationstool.toolscontribution.runtime.ListActivity', {
    extend: 'MF.activation.activities.AbstractActivity',

    requires: [
        'Modera.backend.translationstool.toolscontribution.view.List',
        'Modera.backend.translationstool.toolscontribution.store.Languages'
    ],

    /**
     * @property {MF.runtime.Section} section
     */

    // override
    getId: function() {
        return 'list';
    },

    // override
    isHomeActivity: function() {
        return true;
    },

    // internal
    onSectionLoaded: function(section) {
        section.relayEvents(this, ['handleaction']);
    },

    // override
    doCreateUi: function(params, callback) {
        var me = this;

        me.workbench.getService('config_provider').getConfig(function(config) {

            var toolConfig = config['modera_backend_translations_tool'] || {};
            var filters = toolConfig['filters'] || [];

            var languagesStore = Ext.create('Modera.backend.translationstool.toolscontribution.store.Languages');
            languagesStore.load({
                callback: function() {

                    var translationTokenFilters = filters['translation_token'] || [];
                    if (!params['show'] && translationTokenFilters.length) {
                        params['show'] = translationTokenFilters[0]['id'];
                    }

                    var grid = Ext.create('Modera.backend.translationstool.toolscontribution.view.List', {
                        languagesStore: languagesStore,
                        filters: translationTokenFilters,
                        activeFilter: params['show']
                    });

                    if (params['show']) {
                        grid.getStore().filterByFilterId(params['show']);
                    }

                    callback(grid);
                }
            });

        });
    },

    // override
    applyTransition: function(diff, callback) {
        var me = this;

        if (diff.isParamValueChanged(me, 'show')) {
            var filter = diff.getChangedParamNewValue(me, 'show');
            me.getUi().down('#' + filter).toggle();
            me.getUi().getStore().filterByFilterId(filter);
            callback();
        } else {
            callback();
        }
    },

    // override
    attachStateListeners: function(ui) {
        var me = this;

        ui.down('#header').on('close', function() {
            me.fireEvent('handleaction', 'close');
        });

        ui.on('filterchanged', function(sourceComponent, params) {
            me.executionContext.updateParams(me, {
                show: params.id
            });
            ui.getStore().filterByFilterId(params.id);
        });

        ui.on('import', function() {
            ui.setLoading('Importing...');
            Actions.ModeraBackendTranslationsTool_Translations.import({}, function(r) {
                ui.setLoading(false);
            });
        });

        ui.on('compile', function() {
            ui.setLoading('Compiling...');
            Actions.ModeraBackendTranslationsTool_Translations.compile({}, function(r) {
                ui.setLoading(false);
                ui.setCompileVisible(!r.success);
            });
        });

        ui.on('delete', function(sourceComponent, models) {
            var ids = [];

            ui.setLoading('Deleting...');

            Ext.each(models, function(model) {
                ids.push(model.get('id'));
            });

            if (ids.length) {
                Actions.ModeraBackendTranslationsTool_Translations.remove({
                    filter: [
                        { property: 'id', value: 'in:' + ids.join(',') }
                    ]
                }, function(r) {
                    ui.setLoading(false);
                });
            }
        });

        ui.on('edittranslation', function(sourceComponent, params) {
            me.fireEvent('handleaction', 'edittranslation', sourceComponent, params);
        });

        ui.on('afterlayout', function() {
            var state = me.getState();
            if (state == MF.activation.activities.AbstractActivity.STATE_ACTIVE) {
                var pos = ui.getSelectedCell();
                if (!pos) {
                    pos = ui.findFirstEditableCell();
                }
                if (pos) {
                    ui.selectCell(pos);
                }
            }

            if (!me.checkingCompileNeeded) {
                me.checkingCompileNeeded = true;
                Actions.ModeraBackendTranslationsTool_Translations.isCompileNeeded({}, function(r) {
                    if (r.success) {
                        ui.setCompileVisible(r.status);
                    }
                    me.checkingCompileNeeded = false;
                });
            }
        });
    },

    // override
    wakeUp: function() {
        var me = this;

        var ui = me.getUi();
        var pos = ui.getSelectedCell();
        if (pos) {
            pos = { row: 0, column: 0 };
        } else {
            var am = me.workbench.getActivitiesManager();

            if (am.hasActivity('edit-translation')) {
                var ac = am.getActivityOrDie('edit-translation');

                var id = ac.getExecutionContext().getParam(ac, 'id');
                pos = ui.findCellByTranslationId(id);
            }

            if (!pos) {
                pos = ui.findFirstEditableCell();
            }
        }

        me.callParent(arguments);

        ui.selectCell(pos);
    }
});