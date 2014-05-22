/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.tools.activitylog.runtime.ListActivity', {
    extend: 'MF.activation.activities.AbstractActivity',

    requires: [
        'Modera.backend.tools.activitylog.view.MainPanel',
        'MF.Util'
    ],

    // override
    getId: function() {
        return 'list';
    },

    // override
    isHomeActivity: function() {
        return true;
    },

    // override
    doCreateUi: function(params, callback) {
        var grid = Ext.create('Modera.backend.tools.activitylog.view.MainPanel');
        grid.loadActivities(params);

        callback(grid);
    },

    // override
    attachListeners: function(ui) {
        var me = this;

        var intentMgr = this.workbench.getService('intent_manager');

        ui.on('showactivityentrydetails', function(panel, data) {
            intentMgr.dispatch({
                name: 'show_activity_details',
                params: data
            }, Ext.emptyFn, ['use_first_handler']);
        });

        var header = ui.down('mfc-header');
        if (header) {
            header.on('close', function() {
                me.workbench.activateSection('tools');
            })
        }
    },

    // override
    attachStateListeners: function(ui) {
        var me = this,
            ec = this.getExecutionContext();

        var pagingBar = ui.down('pagingtoolbar');
        if (pagingBar) {
            pagingBar.on('change', function(bar, pageData) {
                if (pageData) {
                    ec.setParam(me, 'page', pageData.currentPage);
                }
            });
        }

        var grid = ui.down('grid');
        if (grid) {
            grid.on('headerclick', function(grid, column) {
                ec.setParams(me, {
                    'sort-by': column.dataIndex,
                    'sort-direction': column.sortState
                });
            });
        }
    },

    // override
    getDefaultParams: function() {
        return {
            page: 1,
            'sort-by': 'createdAt',
            'sort-direction': 'DESC'
        }
    }
});