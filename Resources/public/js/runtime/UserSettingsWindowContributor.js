/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.languages.runtime.UserSettingsWindowContributor', {
    extends: 'MF.runtime.SharedActivitiesProviderInterface',

    requires: [
        'MF.Util',
        'Modera.backend.languages.runtime.UserSettingsWindowActivity'
    ],

    // l10n
    editBtnText: 'Language preference',

    // override
    constructor: function(application) {
        var me = this;

        me.application = application;
        me.activity = Ext.create('Modera.backend.languages.runtime.UserSettingsWindowActivity');

        var workbench = me.application.getContainer().get('workbench');
        workbench.getService('config_provider').getConfig(function(config) {
            var languagesConfig = config['modera_backend_languages'] || {};
            var languages = languagesConfig['languages'] || [];
            if (languages.length > 1) {
                me.contributeButton(
                    'mf-theme-header component[extensionPoint=profileContextMenuActions]',
                    me.onContributedButtonClicked
                );
                me.contributeButton(
                    'modera-backend-security-user-list component[extensionPoint=userActions] menu',
                    me.onUserContributedButtonClicked
                );
            }
        });
    },

    // override
    getSharedActivities: function(section) {
        var me = this;

        return [me.activity];
    },

    // private
    contributeButton: function(query, callback) {
        var me = this;

        var lookup = {};
        lookup[query] =  {
            render: function(menu) {
                menu.add({
                    text: me.editBtnText,
                    contributedBy: me,
                    handler: callback,
                    scope: me
                });
            }
        };

        MF.Util.control(lookup);
    },

    // private
    onContributedButtonClicked: function(btn) {
        var me = this;

        var workbench = me.application.getContainer().get('workbench');
        workbench.getService('config_provider').getConfig(function(config) {
            workbench.launchActivity('edit-language', {
                userId: config['userProfile']['id']
            });
        });
    },

    // private
    onUserContributedButtonClicked: function(btn) {
        var me = this;

        var workbench = me.application.getContainer().get('workbench');
        var users = btn.up('modera-backend-security-user-list').getSelectionModel().getSelection();

        var ids = [];
        Ext.each(users, function(user) {
            ids.push(user.get('id'));
        });
        workbench.launchActivity('edit-language', {
            userId: ids.length > 1 ? ids : ids[0]
        });
    }
});