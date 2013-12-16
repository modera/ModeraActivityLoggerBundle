/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.module.toolscontribution.runtime.ModuleDetailsWindowView', {
    extend: 'MF.viewsmanagement.views.AbstractView',

    requires: [
        'MFC.container.Header',
        'Modera.backend.module.toolscontribution.view.ModuleDetails'
    ],

    // override
    getId: function() {
        return 'module-details-window';
    },

    // override
    doCreateUi: function(params, callback) {
        var me = this;

        Actions.ModeraBackendModule_Default.getModuleDetails({ id: params.id }, function(response) {
            var w = Ext.create('Ext.window.Window', {
                width: 960,
                height: 480,
                modal: true,
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

        Actions.ModeraBackendModule_Default[method]({ id: params.id }, function(response) {
            w.down('panel').setLoading(false);
            w.down('#status').update(response.msg);
            var status = function() {
                Actions.ModeraBackendModule_Default.status(response.status, function(resp) {
                    var html = resp.msg;
                    if (true == resp.working) {
                        setTimeout(function() {
                            status();
                        }, 1000);
                        html += 'Loading ...';
                    }

                    w.down('#status').update(html.replace(/\n/g, "<br />"));
                });
            };
            if (response.success) {
                setTimeout(function() {
                    status();
                }, 500);
            }
        });
    }
});