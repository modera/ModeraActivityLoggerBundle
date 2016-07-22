/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.tools.store.Sections', {
    extend: 'Ext.data.DirectStore',

    constructor: function() {
        this.config = {
            fields: [
                'name', 'glyph', 'iconSrc', 'iconCls', 'description', 'section', { name: 'activationParams', type: 'object' }
            ],
            proxy: {
                type: 'direct',
                directFn: Actions.ModeraBackendTools_Default.getSections
            }
        };
        this.callParent([this.config]);
    }
});