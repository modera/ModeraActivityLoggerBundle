/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.view.user.EditWindow', {
    extend: 'MFC.window.NewAndEditRecordWindow',
    alias: 'widget.modera-backend-security-user-editwindow',

    // l10n
    editRecordTitle: 'Edit user',
    placeHolderText: 'Type here',
    firstNameLabelText: 'First name',
    lastNameLabelText: 'Last name',
    usernameLabelText: 'Principal',
    emailLabelText: 'Email',

    // override
    constructor: function(config) {
        var me = this;

        var defaults = {
            type: 'edit',
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
                    }
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