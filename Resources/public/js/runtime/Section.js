/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.tools.runtime.Section', {
    extend: 'MF.runtime.Section',

    requires: [
        'Modera.backend.tools.runtime.ListView'
    ],

    // override
    activate: function(workbench, callback) {
        workbench.getApplication().loadController('Modera.backend.tools.controller.Controller');

        var listView = Ext.create('Modera.backend.tools.runtime.ListView');

        this.registerViewsManager(workbench, [listView]);

        callback();
    }
});