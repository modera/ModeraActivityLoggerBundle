/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.tools.settings.runtime.Section', {
    extend: 'MF.runtime.Section',

    requires: [
        'Modera.backend.tools.settings.runtime.HostActivity'
    ],

    // override
    activate: function(workbench, callback) {
        var activities = [
            Ext.create('Modera.backend.tools.settings.runtime.HostActivity')
        ];

        this.registerActivitiesManager(workbench, activities);

        callback();
    }
});