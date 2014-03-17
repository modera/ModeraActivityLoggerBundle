/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.module.toolscontribution.runtime.ModuleDetailsWindowActivity', {
    extend: 'MF.activation.activities.AbstractActivity',

    requires: [
        'MFC.container.Header',
        'Modera.backend.module.toolscontribution.view.ModuleDetails'
    ],

    // l10n
    loadingText: 'Loading module details ...',

    // override
    getId: function() {
        return 'module-details-window';
    },

    // override
    doCreateUi: function(params, callback) {
        var me = this;

        var previousActivity = null;
        var activities = this.workbench.getActivitiesManager().getActiveActivities();
        if (activities[activities.length - 1]) {
            previousActivity = activities[activities.length - 1];
            previousActivity.getUi().setLoading(this.loadingText);
        }

        Actions.ModeraBackendModule_Default.getModuleDetails({ id: params.id }, function(response) {
            previousActivity.getUi().setLoading(false);

            var w = Ext.create('Ext.window.Window', {
                width: 960,
                height: 480,
                modal: true,
                resizable: false,
                header: false,
                tbar: {
                    xtype: 'mfc-header',
                    margin: '0 0 9 0',
                    title: response.name,
                    iconSrc: response.logo,
                    closeBtn: true,
                    closeCallback: function() {
                        w.close();
                    }
                },
                layout: 'fit',
                items: {
                    xtype: 'modera-backend-module-moduledetails',
                    dto: response,
                    listeners: {
                        requiremodule: function(con, dto) {
                            w.close();
                            me.callMethod(dto, 'require');
                        },
                        removemodule: function(con, dto) {
                            w.close();
                            me.callMethod(dto, 'remove');
                        }
                    }
                }
            });
            w.show();

            callback(w);
        });
    },

    /**
     * @param params
     * @param method
     */
    callMethod: function(params, method) {
        var w = Ext.create('Ext.window.Window', {
            width: 960,
            height: 480,
            modal: true,
            resizable: false,
            header: false,
            tbar: {
                xtype: 'mfc-header',
                margin: '0 0 9 0',
                title: params.name,
                iconSrc: params.logo,
                closeBtn: true,
                closeCallback: function() {
                    w.close();
                }
            },
            layout: 'fit',
            items: {
                xtype: 'panel',
                bodyPadding: 20,
                border: true,
                autoScroll: true,
                items: {
                    itemId: 'status',
                    html: ''
                }
            }
        });
        w.show();
        w.down('panel').setLoading(true);

        var url = window.location.protocol + '//' + window.location.hostname;
        Actions.ModeraBackendModule_Default[method]({ id: params.id, url: url }, function(response) {
            if (false === response.success) {
                w.down('panel').setLoading(false);
                w.down('#status').update('Package not found!');

                return;
            }

            Ext.data.JsonP.request({
                url: response.urls['call'],
                params: response.params,
                success: function(resp) {

                    w.down('panel').setLoading(false);
                    w.down('#status').update(resp.msg);

                    var status = function() {
                        Ext.data.JsonP.request({
                            url: response.urls['status'],
                            params: response.params,
                            success: function(resp) {
                                var html = resp.msg;
                                if (true == resp.working) {
                                    setTimeout(function() {
                                        status();
                                    }, 500);
                                    html += 'Loading ...';
                                } else {
                                    Actions.ModeraBackendModule_Default.check({ id: params.id }, function(response) {
                                        if (true === response.success) {
                                            html += '<br />PLEASE REFRESH THE PAGE TO SEE CHANGES.<br />';
                                        } else {
                                            html += '<br />AN UNRECOGNIZED ERROR OCCURRED.<br />';
                                        }
                                        w.down('#status').update(html.replace(/\n/g, "<br />"));
                                    });
                                }

                                w.down('#status').update(html.replace(/\n/g, "<br />"));
                            }
                        });
                    };
                    if (resp.success) {
                        setTimeout(function() {
                            status();
                        }, 500);
                    }
                },
                failure: function(resp) {
                    if ('error' === resp) {
                        w.down('panel').setLoading(false);
                        w.down('#status').update('The module server service is not started.');
                    }
                }
            });
        });
    }
});