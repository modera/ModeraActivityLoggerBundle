/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.view.group.GroupUsers', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.modera-backend-security-group-groupusers',

    requires: [
        'Modera.backend.security.toolscontribution.store.GroupUsers',
        'Modera.backend.security.toolscontribution.view.user.List'
    ],

    // l10n
    noGroupSelectedText: 'No group selected',
    userIdColumnText: 'User ID',
    fullNameColumnText: 'Full name',

    // override
    constructor: function(config) {
        var defaults = {
            layout: 'card',
            items: [
                {
                    itemId: 'placeholder',
                    xtype: 'grid',
                    border: true,
                    hideHeaders: true,
                    emptyText: this.noGroupSelectedText,
                    emptyCls: 'mfc-grid-empty-text',
                    columns: [],
                    listeners: {
                        'afterrender': function(grid) {
                            grid.view.refresh();
                        }
                    }
                },
                {
                    itemId: 'users',
                    xtype: 'modera-backend-security-user-list',
                    hideViewAwareComponents: true,
                    columns: [
                        {
                            text: this.userIdColumnText,
                            dataIndex: 'username',
                            flex: 1
                        },
                        {
                            text: this.fullNameColumnText,
                            dataIndex: 'fullname',
                            flex: 1
                        }
                    ],
                    store: Ext.create('Modera.backend.security.toolscontribution.store.GroupUsers')
                }
            ]
        };

        this.config = Ext.apply(defaults, config || {});
        this.callParent([this.config]);

        this.assignListeners();
    },

    // private
    assignListeners: function() {
        this.relayEvents(this.down('modera-backend-security-user-list'), [
            'editrecord', 'deleterecord', 'editgroups'
        ]);
    }
});