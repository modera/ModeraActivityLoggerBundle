/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.translationstool.runtime.ExtJsLocalizationPlugin', {
    extend: 'MF.runtime.extensibility.AbstractPlugin',

    // override
    constructor: function(config) {
        this.callParent(arguments);
        this.config = config;
    },

    // override
    getId: function() {
        return 'extjs_localization_runtime_plugin';
    },

    // override
    bootstrap: function(cb) {
        this.loadScripts(this.config['urls'], function() {
            cb();
        });
    },

    // private
    loadScripts: function(urls, fn) {
        var me = this;
        var url = urls.shift();
        Ext.Loader.loadScript({
            url: url,
            onLoad: function() {
                if (urls.length > 0) {
                    me.loadScripts(urls, fn);
                } else {
                    fn();
                }
            },
            onError: function() {
                console.error('Url "' + url + '" not loaded!');
                fn();
            }
        });
    }
});