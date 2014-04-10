/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.languages.view.UserSettingsWindow', {
    extend: 'MFC.window.NewAndEditRecordWindow',
    alias: 'widget.modera-backend-languages-usersettingswindow',

    // l10n
    editRecordTitle: 'Language preference',

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
                        itemId: 'languages',
                        xtype: 'combo',
                        name: 'language',
                        editable: false,
                        store: Ext.create('Ext.data.Store', {
                            fields: ['id', 'name'],
                            data : []
                        }),
                        queryMode: 'local',
                        displayField: 'name',
                        valueField: 'id'
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