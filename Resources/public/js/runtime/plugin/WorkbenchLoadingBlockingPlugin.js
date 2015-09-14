/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.mjrintegration.runtime.plugin.WorkbenchLoadingBlockingPlugin', {
    extend: 'MF.runtime.extensibility.AbstractPlugin',

    constructor: function() {
        this.isMarkedAsCompleted = false;
    },

    // override
    getId: function() {
        return 'workbench_loading_blocking_plugin';
    },

    // override
    bootstrap: function(cb) {
        this.callback = cb;

        if (this.isMarkedAsCompleted) {
            cb();
        }
    },

    markCompleted: function() {
        this.isMarkedAsCompleted = true;

        this.callback();
    }
});