/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.module.toolscontribution.view.AbstractModulesList', {
    extend: 'Ext.grid.Panel',

    tplIcon: '<img src="{0}" width="32" height="32" alt="" />',
    tplTitle: '<div class="title">{0}</div>{1}',
    tplStatus: '<div class="modera-backend-module-box-status mfc-box-status {0}">{1}</div>',
    tplInfo: '<div class="info">{0}</div>',

    // override
    constructor: function(config) {
        var me = this;
        var defaults = {
            ui: 'rounded',
            boxShadow: true,
            padding: 10,
            border: true,
            hideHeaders: true,
            emptyCls: 'mfc-grid-empty-text',
            cls: 'modera-backend-module-grid',
            monitorModel: 'modera.backend_module_bundle.module',
            listeners: {
                'afterrender': function(grid) {
                    grid.view.refresh();
                }
            },
            columns: [
                {
                    width: 55,
                    dataIndex: 'logo',
                    renderer: function (value, p, record) {
                        return Ext.String.format(me.tplIcon, value);
                    }
                },
                {
                    flex: 1,
                    dataIndex: 'name',
                    renderer: function (value, p, record) {
                        return Ext.String.format(me.tplTitle, value, record.get('description'));
                    }
                },
                {
                    width: 210,
                    dataIndex: 'license',
                    renderer: function(value, p, record) {
                        var status = '';
                        if (record.get('isDependency')) {
                            status = Ext.String.format(this.tplStatus, 'warning', 'dependency');
                        }
                        else if (record.get('updateAvailable')) {
                            status = Ext.String.format(this.tplStatus, 'warning', 'update');
                        }
                        else if (record.get('installed')) {
                            status = Ext.String.format(this.tplStatus, 'success', 'installed');
                        }

                        return Ext.String.format(this.tplInfo, status);
                    }
                }
            ]
        };

        me.config = Ext.apply(defaults, config || {});
        me.callParent([me.config]);
    }
});