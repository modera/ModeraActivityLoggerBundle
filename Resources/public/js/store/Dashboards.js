/**
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */
Ext.define('Modera.backend.dashboard.store.Dashboards', {
    extend: 'Ext.data.DirectStore',

    constructor: function() {
        this.config = {
            fields: [
                'name', 'label', 'uiClass', 'default'
            ],
            proxy: {
                type: 'direct',
                directFn: Actions.ModeraBackendDashboard_Default.getDashboards
            }
        };
        this.callParent([this.config]);
    }
});