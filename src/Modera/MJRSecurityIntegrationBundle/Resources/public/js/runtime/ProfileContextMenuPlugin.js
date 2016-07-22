/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.mjrsecurityintegration.runtime.ProfileContextMenuPlugin', {
    extend: 'MF.runtime.extensibility.AbstractPlugin',

    requires: [
        'Ext.menu.Menu'
    ],

    // override
    init: function() {
        var me = this;
        me.callParent(arguments);

        me.control({
            'mf-theme-header': {
                showprofile: me.onShowProfile
            }
        });
    },

    // override
    getId: function() {
        return 'profile-context-menu'
    },

    // override
    bootstrap: function(callback) {
        var me = this;
        callback();
    },

    // private
    onShowProfile: function(header) {
        var btn = header.down('#showProfileBtn');
        if (!btn._profileContextMenuActions) {
            btn._profileContextMenuActions = Ext.widget({
                xtype: 'menu',
                extensionPoint: 'profileContextMenuActions',
                items: []
            });
        }
        btn._profileContextMenuActions.showBy(btn);
    }
});