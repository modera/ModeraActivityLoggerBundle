/**
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */
Ext.define('Modera.backend.dashboard.view.DashboardPanel', {
    extend: 'Ext.container.Container',
    alias: 'widget.modera-backdashboard-dashboardpanel',

    // override
    constructor: function(config) {
        var defaults = {
            layout: 'card',
            defaults: {
                border: false,
                layout: 'fit'
            }
        };

        this.config = Ext.apply(defaults, config || {});
        this.callParent([this.config]);
    },

    /**
     * @param {String} name  If NULL is given then we will try to find a default one
     */
    showDashboard: function(name) {
        var oldActivityContainer = this.getLayout().getActiveItem(),
            newActivityContainer = this.down(Ext.String.format('component[activity={0}]', name || 'default'));

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
    }
});