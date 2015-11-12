/**
 * Plugin dynamically injects api.js which declares ExtDirect definition.
 *
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.mjrsecurityintegration.runtime.ExtDirectApiScriptInjectorPlugin', {
    extend: 'MF.runtime.extensibility.AbstractPlugin',

    /**
     * @private {String} directApiUrl
     */

    /**
     * @param {Object} config
     */
    constructor: function(config) {
        MF.Util.validateRequiredConfigParams(this, config, ['directApiUrl']);

        Ext.apply(this, config);
    },

    // override
    bootstrap: function(callback) {
        var script = document.createElement('script');

        script.setAttribute('src', this.directApiUrl);
        script.setAttribute('id', 'modera_direct_api');
        script.onload = function() {
            callback();
        };

        document.head.appendChild(script);
    }
});
