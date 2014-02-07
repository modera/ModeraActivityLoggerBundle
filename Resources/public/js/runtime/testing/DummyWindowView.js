/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.dashboard.runtime.testing.DummyWindowView', {
    extend: 'MF.viewsmanagement.views.AbstractCompositeView',

    // override
    getId: function() {
        return 'dummy-window';
    },

    // protected
    getZones: function(callback) {
        if (!this.zones) {
            var statsView = Ext.create('Modera.backend.dashboard.runtime.testing.StatsView'),
                groups = Ext.create('Modera.backend.security.toolscontribution.runtime.group.ListView'),
                users = Ext.create('Modera.backend.security.toolscontribution.runtime.user.ListView'),
                sample = Ext.create('Modera.backend.dashboard.runtime.DashboardsView');

            this.zones = [
                {
                    controllingParam: 'tab',
                    targetContainerResolver: 'tabpanel',
                    activities: {
                        stats: statsView,
                        groups: groups,
                        users: users,
                        dashboard: sample
                    },
                    controller: function(parentUi, zoneUi, dashboardName, callback) {
                        var oldActivityUi = zoneUi.getLayout().getActiveItem(),
                            newActivityUi = zoneUi.down(Ext.String.format('component[activity={0}]', dashboardName));

                        zoneUi.getLayout().setActiveItem(newActivityUi);

                        zoneUi.fireEvent('activitychange', zoneUi, newActivityUi, oldActivityUi);

                        callback();
                    }
                }
            ];
        }

        callback(this.zones);
    },

    // override
    doCreateUi: function(params, callback) {
        var w = Ext.create('Ext.window.Window', {
            title: 'Dummy window',
            width: 700,
            modal: true,
            layout: {
                type: 'vbox',
                align: 'stretch',
                pack: 'stretch'
            },
            items: [
                {
                    xtype: 'form',
                    items: [
                        {
                            xtype: 'fieldset',
                            title: 'General',
                            items: [
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Firstname'
                                },
                                {
                                    xtype: 'textfield',
                                    fieldLabel: 'Lastname'
                                }
                            ]
                        }
                    ]
                },
                {
                    xtype: 'tabpanel',
                    height: 300,
                    border: true,
                    items: [
                        {
                            title: 'Stats',
                            activity: 'stats'
                        },
                        {
                            title: 'Users',
                            activity: 'users'
                        },
                        {
                            title: 'Groups',
                            activity: 'groups'
                        },
                        {
                            title: 'Dashboard',
                            activity: 'dashboard'
                        }
                    ]
                }
            ]
        });

        w.down('tabpanel').on('tabchange', function(tabPanel, newCard, oldCard) {
            tabPanel.fireEvent('activitychange', tabPanel, newCard, oldCard);
        });

        this.setUpZones(w);

        callback(w);
    }
});