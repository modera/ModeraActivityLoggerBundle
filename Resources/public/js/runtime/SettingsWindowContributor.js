/**
 * Contributes a button to Tools / Security / Users which allows to activate a window to edit group dashboard
 *
 * @author Alex Rudakov <alexandr.rudakov@modera.org>
 * @copyright 2014 Modera Foundation
 */
Ext.define('Modera.backend.dashboard.runtime.SettingsWindowContributor', {
    extends: 'MF.runtime.SharedViewsProviderInterface',

    requires: [
        'Modera.backend.security.toolscontribution.runtime.Section',
        'Modera.backend.dashboard.runtime.UserDashboardSettingsWindowView',
        'Modera.backend.dashboard.runtime.GroupDashboardSettingsWindowView',
        'MF.Util'
    ],

    // l10n
    settingsBtnText: 'Dashboards settings',

    // override
    constructor: function(application) {
        this.application = application;

        this.userSettingsWindowView = Ext.create('Modera.backend.dashboard.runtime.UserDashboardSettingsWindowView');
        this.groupSettingsWindowView = Ext.create('Modera.backend.dashboard.runtime.GroupDashboardSettingsWindowView');

        this.contributeButton('modera-backend-security-user-list', 'userActions', this.onUserContributedButtonClicked);
        this.contributeButton('modera-backend-security-group-overview #groups', 'groupActions', this.onGroupContributedButtonClicked);
    },

    // private
    contributeButton: function(uiCmp, extensionPoint, callback) {
        var me = this;

        var query = uiCmp + ' component[extensionPoint=' + extensionPoint + '] menu';

        var lookup = {};
        lookup[query] =  {
            render: function(menu) {
                menu.add({
                    text: me.settingsBtnText,
                    contributedBy: me,
                    handler: callback,
                    scope: me
                })
            }
        };

        MF.Util.control(lookup);
    },

    // private
    onUserContributedButtonClicked: function(btn) {
        var user = btn.up('modera-backend-security-user-list').getSelectionModel().getSelection()[0];

        this.application.getContainer().get('workbench').activateView('user-dashboard-settings', {
            id: user.get('id')
        });
    },

    // private
    onGroupContributedButtonClicked: function(btn) {
        var group = btn.up('modera-backend-security-group-overview #groups').getSelectionModel().getSelection()[0];

        this.application.getContainer().get('workbench').activateView('group-dashboard-settings', {
            id: group.get('id')
        });
    },

    // override
    getSharedViews: function(section) {
        if (section instanceof Modera.backend.security.toolscontribution.runtime.Section) {
            return [this.userSettingsWindowView, this.groupSettingsWindowView];
        }

        return [];
    }
});