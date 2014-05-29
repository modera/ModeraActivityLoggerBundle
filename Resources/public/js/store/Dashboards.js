/**
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */
Ext.define('Modera.backend.dashboard.store.Dashboards', {
    extend: 'Ext.data.Store',

    constructor: function() {
        this.config = {
            autoLoad: true,
            fields: [
                'name', //
                'label',
                'uiClass',
                'default'//,
//                'glyph',
//                'iconSrc',
//                'iconCls'
            ],
            proxy: {
                type: 'memory',
                reader: {
                    type: 'json'
                }
            }
        };
        this.callParent([this.config]);
    }
});