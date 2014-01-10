/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.view.group.NewAndEditWindow', {
    extend: 'MFC.window.NewAndEditRecordWindow',
    alias: 'widget.modera-backend-security-group-newwindow',

    requires: [
    ],

    // l10n
    placeHolderText: 'Type here',
    nameFieldText: 'Name',

    // override
    constructor: function(config) {
        var me = this;

        var defaults = {
            resizable: false,
            autoScroll: true,
            width: 500,
            maxHeight: Ext.getBody().getViewSize().height - 60,
            items: {
                xtype: 'form',
                groupName: 'main-form',
                // see MFC.GroupedDataLoader
                loadData: function(data) {
                    this.getForm().setValues(data);
                },
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
                        name: 'name',
                        fieldLabel: me.firstNameLabelText,
                        emptyText: me.nameFieldText
                    }
                ]
            }
        };

        this.config = Ext.apply(defaults, config || {});
        this.callParent([this.config]);
    }
});