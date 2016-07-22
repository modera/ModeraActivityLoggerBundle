/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.dcmjr.view.GeneralSettingsPanel', {
    extend: 'Ext.panel.Panel',

    requires: [
        'MFC.form.field.InPlace',
        'MFC.form.field.OnOff'
    ],

    // l10n
    siteTitleLabelText: 'Site title',
    primaryAddressLabelText: 'Primary address',
    defaultSectionLabelText: 'Default section',
    developmentModeLabelText: 'Development mode',
    maintenanceModeLabelText: 'Maintenance mode',

    /**
     * @param {Object} config
     */
    constructor: function(config) {
        var defaults = {
            frame: true,
            bodyPadding: 35,
            items: {
                xtype: 'form',
                defaults: {
                    labelAlign: 'right',
                    labelWidth: 150,
                    anchor: '100%'
                },
                items: [
                    {
                        name: 'site_name',
                        fieldLabel: this.siteTitleLabelText,
                        xtype: 'mfc-inplacefield'
                    },
                    {
                        name: 'url',
                        fieldLabel: this.primaryAddressLabelText,
                        xtype: 'mfc-inplacefield'
                    },
                    {
                        name: 'home_section',
                        fieldLabel: this.defaultSectionLabelText,
                        xtype: 'mfc-inplacefield'
                    },
                    {
                        xtype: 'mfc-onofffield',
                        fieldLabel: this.developmentModeLabelText,
                        name: 'kernel_env',
                        onValue: 'dev',
                        offValue: 'prod'
                    },
                    {
                        xtype: 'mfc-onofffield',
                        fieldLabel: this.maintenanceModeLabelText,
                        name: 'kernel_debug',
                        onValue: 'true',
                        offValue: 'false'
                    }
                ]
            }
        };

        this.config = Ext.apply(defaults, config || {});

        this.callParent([this.config]);
    }
});