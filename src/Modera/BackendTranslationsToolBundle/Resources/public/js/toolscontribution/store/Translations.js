/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.translationstool.toolscontribution.store.Translations', {
    extend: 'Ext.data.DirectStore',

    constructor: function() {
        this.config = {
            fields: [
                'id', 'source', 'bundleName', 'domain', 'tokenName', 'isObsolete', 'translations'
            ],
            remoteFilter: true,
            remoteSort: true,
            proxy: {
                type: 'direct',
                directFn: Actions.ModeraBackendTranslationsTool_Translations.listWithFilters,
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
    },

    /**
     * @param {String} filterId
     */
    filterByFilterId: function(filterId) {
        this.filters.clear();
        this.filter({ property: '__filter__', value: filterId });
    }
});