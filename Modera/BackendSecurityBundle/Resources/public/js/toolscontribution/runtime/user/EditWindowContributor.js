/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.runtime.user.EditWindowContributor', {
    extends: 'MF.runtime.SharedActivitiesProviderInterface',

    requires: [
        'MF.Util',
        'Modera.backend.security.toolscontribution.runtime.user.EditWindowActivity'
    ],

    // l10n
    editUserBtnText: 'Edit user',

    // override
    constructor: function(application) {
        var me = this;

        me.application = application;
        me.editWindowActivity = Ext.create('Modera.backend.security.toolscontribution.runtime.user.EditWindowActivity');

        me.contributeButton('mf-theme-header', 'profileContextMenuActions', this.onContributedButtonClicked);
    },

    // override
    getSharedActivities: function(section) {
        var me = this;

        return [me.editWindowActivity];
    },

    // private
    contributeButton: function(uiCmp, extensionPoint, callback) {
        var me = this;

        var query = uiCmp + ' component[extensionPoint=' + extensionPoint + ']';

        var lookup = {};
        lookup[query] =  {
            render: function(menu) {
                menu.add({
                    text: me.editUserBtnText,
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
            workbench.launchActivity('edit-user', {
                id: config['userProfile']['id']
            });
        });
    }
});