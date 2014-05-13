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
        if (!btn.menu) {
            btn.showEmptyMenu = true;
            btn.menu = Ext.widget({
                xtype: 'menu',
                extensionPoint: 'profileContextMenuActions',
                items: []
            });
            btn.menu.ownerButton = btn;
            btn.mon(btn.menu, {
                scope: btn,
                show: btn.onMenuShow,
                hide: btn.onMenuHide
            });
            btn.keyMap = new Ext.util.KeyMap({
                target: btn.el,
                key: Ext.EventObject.DOWN,
                handler: btn.onDownKey,
                scope: btn
            });
            btn.showMenu();
        }

    }
});