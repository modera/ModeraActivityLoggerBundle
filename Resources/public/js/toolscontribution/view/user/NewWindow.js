/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.view.user.NewWindow', {
    extend: 'MFC.window.NewAndEditRecordWindow',
    alias: 'widget.modera-backend-security-user-newwindow',

    // l10n
    newRecordTitle: 'Create new user',
    placeHolderText: 'Type here',
    firstNameLabelText: 'First name',
    lastNameLabelText: 'Last name',
    usernameLabelText: 'Principal',
    emailLabelText: 'Email',
    passwordLabelText: 'Password',

    // override
    constructor: function(config) {
        var me = this;

        var defaults = {
            type: 'new',
            groupName: 'main-form',
            resizable: false,
            autoScroll: true,
            width: 500,
            maxHeight: Ext.getBody().getViewSize().height - 60,
            items: {
                xtype: 'form',
                defaultType: 'textfield',
                layout: {
                    type: 'vbox',
                    align: 'stretch'
                },
                defaults: {
                    labelAlign: 'right'
                },
                items: [
                    {
                        xtype: 'hiddenfield',
                        name: 'id'
                    },
                    {
                        name: 'firstName',
                        fieldLabel: me.firstNameLabelText,
                        emptyText: me.placeHolderText
                    },
                    {
                        name: 'lastName',
                        fieldLabel: me.lastNameLabelText,
                        emptyText: me.placeHolderText
                    },
                    {
                        name: 'username',
                        fieldLabel: me.usernameLabelText,
                        emptyText: me.placeHolderText
                    },
                    {
                        name: 'email',
                        fieldLabel: me.emailLabelText,
                        emptyText: me.placeHolderText
                    }/*,
                    {
                        name: 'plainPassword',
                        fieldLabel: me.passwordLabelText,
                        emptyText: me.placeHolderText
                    }*/
                ]
            }
        };

        this.config = Ext.apply(defaults, config || {});
        this.callParent([this.config]);
    },

    loadData: function(data) {
        var me = this;
        me.down('form').getForm().setValues(data);
    }
});