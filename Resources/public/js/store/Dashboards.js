/**
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */
Ext.define('Modera.backend.dashboard.store.Dashboards', {
    extend: 'Ext.data.Store',

    constructor: function() {
        this.config = {
            fields: [
                'name', 'label', 'uiClass', 'default'
            ],
            proxy: {
                type: 'memory'
            }
        };
        this.callParent([this.config]);
    }
});