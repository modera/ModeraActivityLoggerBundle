/**
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */
Ext.define('Modera.backend.dashboard.runtime.SampleDashboardView', {
    extend: 'MF.activation.activities.AbstractActivity',

    // override
    getId: function() {
        return 'sample-dashboard';
    },

    // override
    doCreateUi: function(params, callback) {
        var me = this;

        var ui = Ext.create('Ext.panel.Panel', {
            layout: 'fit',
            items: [
                {
                    layout: 'fit',
                    height: 600,
                    xtype: 'chart',
                    style: 'background:#fff',
                    animate: true,
                    shadow: true,
                    store: Ext.create('Ext.data.JsonStore', {
                        fields: ['name', 'data1', 'data2', 'data3', 'data4', 'data5', 'data6', 'data7', 'data9', 'data9'],
                        data: me.generateData()
                    }),
                    axes: [{
                        type: 'Numeric',
                        position: 'left',
                        fields: ['data1'],
                        label: {
                            renderer: Ext.util.Format.numberRenderer('0,0')
                        },
                        title: 'Number of Hits',
                        grid: true,
                        minimum: 0
                    }, {
                        type: 'Category',
                        position: 'bottom',
                        fields: ['name'],
                        title: 'Month of the Year'
                    }
                    ],
                    series: [{
                        type: 'column',
                        axis: 'left',
                        highlight: true,
                        tips: {
                            trackMouse: true,
                            renderer: function(storeItem, item) {
                                this.setTitle(storeItem.get('name') + ': ' + storeItem.get('data1') + ' $');
                            }
                        },
                        label: {
                            display: 'insideEnd',
                            'text-anchor': 'middle',
                            field: 'data1',
                            renderer: Ext.util.Format.numberRenderer('0'),
                            orientation: 'vertical',
                            color: '#333'
                        },
                        xField: 'name',
                        yField: 'data1'
                    }]
                }
            ]
        });

        callback(ui);
    },

    // private
    generateData: function(n, floor){
        var data = [],
            p = (Math.random() *  11) + 1,
            i;

        floor = (!floor && floor !== 0)? 20 : floor;

        for (i = 0; i < (n || 12); i++) {
            data.push({
                name: Ext.Date.monthNames[i % 12],
                data1: Math.floor(Math.max((Math.random() * 100), floor)),
                data2: Math.floor(Math.max((Math.random() * 100), floor)),
                data3: Math.floor(Math.max((Math.random() * 100), floor)),
                data4: Math.floor(Math.max((Math.random() * 100), floor)),
                data5: Math.floor(Math.max((Math.random() * 100), floor)),
                data6: Math.floor(Math.max((Math.random() * 100), floor)),
                data7: Math.floor(Math.max((Math.random() * 100), floor)),
                data8: Math.floor(Math.max((Math.random() * 100), floor)),
                data9: Math.floor(Math.max((Math.random() * 100), floor))
            });
        }
        return data;
    }
});