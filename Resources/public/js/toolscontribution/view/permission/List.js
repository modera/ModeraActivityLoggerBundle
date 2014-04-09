/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.view.permission.List', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.modera-backend-security-permission-list',

    requires: [
        'Modera.backend.security.toolscontribution.store.Permissions'
    ],

    // override
    constructor: function(config) {
        var me = this;

        var columns = [];
        var groupsStore = config['groupsStore'];
        groupsStore.each(function(group) {
            columns.push(me.getCheckerColumnConfig({
                groupId: group.get('id'),
                text: group.get('name')
            }));
        });

        var store = Ext.create('Modera.backend.security.toolscontribution.store.Permissions');

        var defaults = {
            frame: true,
            rounded: true,
            columnLines: true,
            monitorModel: 'modera.security_bundle.user',
            emptyCls: 'mfc-grid-empty-text',
            store: store,
            features: [{
                ftype:'grouping',
                groupHeaderTpl: '{name}'
            }],
            viewConfig: {
                markDirty:false
            },
            security: {
                role: 'ROLE_MANAGE_PERMISSIONS'
            },
            columns: [
                {
                    dataIndex: 'name',
                    flex: 4,
                    sortable: false,
                    hideable: false,
                    closable: false
                }
            ].concat(columns)
        };

        me.config = Ext.apply(defaults, config || {});
        me.callParent([me.config]);

        me.addEvents(
            /**
             * @event permissionchange
             * @param {Modera.backend.security.toolscontribution.view.permission.List} me
             * @param {Object} params
             */
            'permissionchange'
        );

        me.assignListeners();
    },

    // private
    assignListeners: function() {
        var me = this;

        me.on('beforeselect', function(sm, record, index) {
            return false;
        });

        me.on('cellclick', function(view, td, cellIndex, record, tr, rowIndex, e) {
            var column = me.columns[cellIndex];
            if (column['groupId']) {
                me.toggleChecker(tr, column, record);
            }
        });
    },

    // private
    getCheckerColumnConfig: function(config) {
        return Ext.apply({
            flex: 1,
            dataIndex: 'groups',
            clickTargetName: 'el',
            sortable: false,
            draggable: false,
            resizable: false,
            hideable: false,
            menuDisabled: true,
            align: 'center',
            tdCls: Ext.baseCSSPrefix + 'grid-cell-checkcolumn',
            innerCls: Ext.baseCSSPrefix + 'grid-cell-inner-checkcolumn',
            renderer : function(values, meta) {
                var cssPrefix = Ext.baseCSSPrefix;
                var cls = [cssPrefix + 'grid-checkcolumn', 'group-' + config['groupId']];

                meta.style = 'cursor:pointer;';

                if (this.disabled) {
                    meta.tdCls += ' ' + this.disabledCls;
                }

                var checked = values.indexOf(config['groupId']) !== -1;
                if (checked) {
                    cls.push(cssPrefix + 'grid-checkcolumn-checked');
                }
                return '<img class="' + cls.join(' ') + '" src="' + Ext.BLANK_IMAGE_URL + '"/>';
            },
            locked: false
        }, config);
    },

    // private
    toggleChecker: function(node, column, record) {
        var me = this;
        var view = me.getView();
        var cssPrefix = Ext.baseCSSPrefix;

        var checkbox = Ext.fly(node).down('.' + cssPrefix + 'grid-checkcolumn.group-' + column['groupId']);
        if (checkbox.hasCls(cssPrefix + 'grid-checkcolumn-checked')) {
            checkbox.removeCls(cssPrefix + 'grid-checkcolumn-checked');
        } else {
            checkbox.addCls(cssPrefix + 'grid-checkcolumn-checked');
        }

        var groups = [];
        var checkboxes = Ext.fly(node).query('.' + cssPrefix + 'grid-checkcolumn.' + cssPrefix + 'grid-checkcolumn-checked');
        Ext.each(checkboxes, function(checkbox) {
            Ext.each(checkbox.className.split(' '), function(cls) {
                if (cls.indexOf('group-') !== -1) {
                    groups.push(parseInt(cls.replace('group-', '')));
                }
            });
        });

        record.set('groups', groups);

        me.fireEvent('permissionchange', me, {
            id: record.get('id'),
            groups: groups
        });
    }
});