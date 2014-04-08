/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.dashboard.runtime.UserDashboardSettingsWindowContributor', {
    extends: 'MF.runtime.SharedActivitiesProviderInterface',

    requires: [
        'MF.Util',
        'Modera.backend.dashboard.runtime.UserDashboardSettingsWindowActivity'
    ],

    // l10n
    dashboardSettingsBtnText: 'Dashboard settings',

    // override
    constructor: function(application) {
        var me = this;

        me.application = application;
        me.userDashboardSettingsWindowActivity = Ext.create('Modera.backend.dashboard.runtime.UserDashboardSettingsWindowActivity');

        me.contributeButton('mf-theme-header', 'profileContextMenuActions', this.onContributedButtonClicked);
    },

    // override
    getSharedActivities: function(section) {
        var me = this;

        return [me.userDashboardSettingsWindowActivity];
    },

    // private
    contributeButton: function(uiCmp, extensionPoint, callback) {
        var me = this;

        var query = uiCmp + ' component[extensionPoint=' + extensionPoint + ']';

        var lookup = {};
        lookup[query] =  {
            render: function(menu) {
                menu.add({
                    text: me.dashboardSettingsBtnText,
                    contributedBy: me,
                    handler: callback,
                    scope: me
                })
            }
        };

        MF.Util.control(lookup);
    },

    // private
    onContributedButtonClicked: function(btn) {
        var me = this;

        var workbench = me.application.getContainer().get('workbench');
        workbench.getService('config_provider').getConfig(function(config) {
            workbench.launchActivity('user-dashboard-settings', {
                id: config['userProfile']['id']
            });
        });
    }
});