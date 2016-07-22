/**
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */
Ext.define('Modera.backend.dashboard.view.DefaultDashboardPanel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.modera-backdashboard-dashboardpanel-default',


    requires: [
        'Ext.data.Store'
    ],

    // l10n
    nothingToDisplayText: 'Nothing to display',

    // override
    constructor: function(config) {
        var me = this;
        var defaults = {
            basePanel: true,
            layout: 'fit',
            bodyPadding: 10,
            items: {
                xtype: 'dataview',
                multiSelect: false,
                singleSelect: true,
                cls: 'modera-backend-tools',
                tpl: new Ext.XTemplate(
                    '<tpl if="values.length &gt; 0">',
                        '<ul>',
                            '<tpl for=".">',
                                '<li>',
                                    '<table class="container">',
                                        '<tr><td>',
                                            '{[this.renderIcon(values)]}',
                                        '</td><td>',
                                            '<h2>{label}</h2>',
                                            '<p>{description}</p>',
                                        '</td></tr>',
                                    '</table>',
                                '</li>',
                            '</tpl>',
                        '</ul>',
                    '<tpl else>',
                        '<table class="empty"><tr><td>',
                            '<p>' + me.nothingToDisplayText + '</p>',
                        '</td></tr></table>',
                    '</tpl>', {
                        renderIcon: function(values) {
                            var iconCls = '';
                            if (values.iconCls && values.iconCls.length) {
                                iconCls = values.iconCls;
                            }
                            var iconSrc = '';
                            if (values.iconSrc && values.iconSrc.length) {
                                iconSrc = values.iconSrc;
                            }
                            var glyph = '';
                            if (values.glyph && values.glyph.length) {
                                glyph = values.glyph;
                            }
                            if (!iconCls && !glyph && !iconSrc) {
                                glyph = FontAwesome.resolve('cogs');
                            }

                            if (glyph) {
                                var nid = Ext.id();
                                Ext.Function.defer(function(glyph, id) {
                                    Ext.widget({
                                        xtype: 'image',
                                        glyph: glyph,
                                        renderTo: id
                                    });
                                }, 100, me, [glyph, nid]);
                                return '<div class="glyph-el ' + iconCls + '" id="' + nid + '"></div>';
                            } else if (iconSrc) {
                                var nid = Ext.id();
                                Ext.Function.defer(function(iconSrc, id) {
                                    Ext.widget({
                                        xtype: 'image',
                                        src: iconSrc,
                                        renderTo: id
                                    });
                                }, 100, me, [iconSrc, nid]);
                                return '<div class="icon-el ' + iconCls + '" id="' + nid + '"></div>';
                            } else if (iconCls) {
                                return '<div class="icon-el ' + iconCls + '"></div>';
                            } else {
                                return '';
                            }
                        }
                    }
                ),
                itemSelector: 'li',
                store:  Ext.create('Ext.data.Store', {
                    autoLoad: true,
                    data : {'dashboards': config.dashboards},
                    fields: [
                        'name', //
                        'label',
                        'uiClass',
                        'default',
                        'description',

                        'glyph',
                        'iconSrc',
                        'iconCls'
                    ],
                    proxy: {
                        type: 'memory',
                        reader: {
                            type: 'json',
                            root: 'dashboards'
                        }
                    }
                })
            }
        };


        this.config = Ext.apply(defaults, config || {});
        this.callParent([this.config]);

        this.addEvents(
            /**
             * @event changedashboard
             * @param {Modera.backend.dashboard.view.DefaultDashboardPanel} me
             */
            'changedashboard'
        );

        this.assignListeners();
    },

    getStore: function() {
        var me = this;
        return me.down('dataview').getStore();
    },

    // private
    assignListeners: function() {
        var me = this;
        me.down('dataview').on('selectionchange', function(dataView, selections) {
            if (selections.length) {
                var record = selections[0];
                me.fireEvent('changedashboard', me, record);            }
        });
    }

});