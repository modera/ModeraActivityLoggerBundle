/**
 * This plugin monitors MJR and if a newly loaded section has "getTitle" method then a value returned by it will
 * be used in page's title.
 *
 * @see Modera.mjrintegration.runtime.titlehandling.PageTitleManager
 *
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.mjrintegration.runtime.titlehandling.MonitoringPlugin', {
    extend: 'MF.runtime.extensibility.AbstractPlugin',

    requires: [
        'MF.Util',
        'Ext.XTemplate'
    ],

    /**
     * @private
     * @property {Modera.mjrintegration.runtime.titlehandling.PageTitleManager} pageTitleMgr
     */

    /**
     * @param {Object} config
     */
    constructor: function (config) {
        MF.Util.validateRequiredConfigParams(this, config, ['pageTitleMgr']);

        Ext.apply(this, config);
    },

    // override
    getId: function() {
        return 'page_title_monitoring_plugin';
    },

    // override
    bootstrap: function(cb) {
        var me = this;

        var app = this.application,
            eb = app.getContainer().get('event_bus'),
            workbench = app.getContainer().get('workbench');

        eb.on('runtime.section_changed', function() {
            me.pageTitleMgr.updateTitle();
        });

        eb.on('runtime.section_loaded', function() {
            me.pageTitleMgr.updateTitle();
        });

        cb();
    }
});