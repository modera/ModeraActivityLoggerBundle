/**
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */
Ext.define('Modera.backend.dashboard.view.DashboardPanel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.modera-backdashboard-dashboardpanel',

    requires: [
        'Modera.backend.dashboard.store.Dashboards',
        'MFC.container.Header'
    ],

    // l10n
    nothingToDisplayText: 'No dashboards',
    titleText: 'Dashboard',

    // override
    constructor: function(config) {
        var defaults = {
            basePanel: true,
            padding: 10,
            border: true,
            ui: 'rounded',
            boxShadow: true,
            layout: 'card',
            dockedItems: [
                {
                    xtype: 'mfc-header',
                    dock: 'top',
                    title: this.titleText,
                    margin: '0 0 9 0',
                    items: [
                        '->',
                        {
                            queryMode: 'local',
                            xtype: 'combo',
                            itemId: 'dashboard-select',
                            fieldLabel: 'Select dashboard',
                            displayField: 'label',
                            valueField: 'name',
                            width: 500,
                            labelWidth: 130,
                            store: Ext.create('Modera.backend.dashboard.store.Dashboards')
                        }
                    ]
                }
            ]
        };

        this.config = Ext.apply(defaults, config || {});
        this.callParent([this.config]);

        this.assignListeners();
    },

    // private
    assignListeners: function() {
        var me = this;

        me.down('#dashboard-select').on('select', function(combo, selections) {
            if (selections.length) {
                me.showDashboard(selections[0].get('name'));
            }
        });
    },

    /**
     * @param {String} name  If NULL is given then we will try to find a default one
     */
    showDashboard: function(name) {
        var dashboardsCombo = this.down('#dashboard-select'),
            store = dashboardsCombo.getStore();

        name = name || store.findRecord('default', true).get('name');

        dashboardsCombo.setValue(store.findRecord('name', name));

        var oldActivityContainer = this.getLayout().getActiveItem(),
            newActivityContainer = this.down(Ext.String.format('component[activity={0}]', name));

        this.getLayout().setActiveItem(newActivityContainer);

        this.fireEvent('activitychange', this, newActivityContainer, oldActivityContainer);
    },

    /**
     * Prepares containers where dashboards will be rendered to
     *
     * @param {Object[]} dashboards
     */
    setDashboards: function(dashboards) {
        var me = this;

        Ext.each(dashboards, function(dashboard) {
            me.add({
                xtype: 'container',
                activity: dashboard.name
            })
        });

        me.down('#dashboard-select').getStore().loadData(dashboards);
    }
});