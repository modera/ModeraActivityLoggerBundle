/**
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */
Ext.define('Modera.backend.dashboard.view.MotivationDashboard', {
    extend: 'Ext.container.Container',

    requires: [
        'Modera.backend.dashboard.store.Dashboards',
        'MFC.container.Header'
    ],

    // override
    constructor: function(config) {
        var defaults = {
            layout: 'fit',
            items: [{
                xtype: 'image',
                src: 'http://www.nastol.com.ua/pic/201205/1024x768/nastol.com.ua-23203.jpg'
            }]
        };
        this.config = Ext.apply(defaults, config || {});
        this.callParent([this.config]);
    }
});