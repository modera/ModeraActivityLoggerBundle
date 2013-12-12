/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.module.toolscontribution.view.AbstractModulesList', {
    extend: 'Ext.grid.Panel',

    tplIcon: '<img src="{0}" width="32" height="32" alt="" />',
    tplTitle: '<div class="title">{0}</div>{1}',
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
                        if (record.get('updateAvailable')) {
                            status = 'update';
                        }
                        else if (record.get('installed')) {
                            status = 'installed';
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