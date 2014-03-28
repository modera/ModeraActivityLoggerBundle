/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.translationstool.toolscontribution.store.Languages', {
    extend: 'Ext.data.DirectStore',

    constructor: function() {
        this.config = {
            fields: [
                'id', 'name', 'locale', 'isEnabled'
            ],
            proxy: {
                type: 'direct',
                directFn: Actions.ModeraBackendTranslationsTool_Languages.list,
                extraParams: {
                    hydration: {
                        profile: 'list'
                    }
                },
                reader: {
                    root: 'items'
                }
            },
            autoLoad: false
        };
        this.callParent([this.config]);
    }
});