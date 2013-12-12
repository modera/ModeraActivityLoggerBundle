/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.module.toolscontribution.runtime.ModuleDetailsWindowView', {
    extend: 'MF.viewsmanagement.views.AbstractView',

    requires: [
        'MFC.container.Header'
    ],

    // override
    getId: function() {
        return 'module-details-window';
    },

    // override
    doCreateUi: function(params, callback) {
        var me = this;

        Actions.ModeraBackendModule_Default.getModuleDetails({ id: params.id }, function(response) {
            var items = [];
            Ext.iterate(response, function(key, val) {
                items.push({
                    xtype: 'displayfield',
                    fieldLabel: key,
                    value: val
                });
            });

            var w = Ext.create('Ext.window.Window', {
                width: 960,
                height: 480,
                modal: true,
                header: false,
                tbar: {
                    xtype: 'mfc-header',
                    title: response.name,
                    iconSrc: response.logo,
                    closeBtn: true,
                    closeCallback: function() {
                        w.close();
                    }
                },
                items: {
                    xtype: 'form',
                    bodyPadding: 20,
                    items: items
                },
                bbar: [
                    {
                        disabled: (response.installed && !response.updateAvailable),
                        xtype: 'button',
                        text: (response.updateAvailable ? 'Update' : 'Install'),
                        handler: function() {
                            w.close();
                            me.callMethod(response, 'require');
                        }
                    },
                    '->',
                    {
                        disabled: !response.installed,
                        xtype: 'button',
                        text: 'Remove',
                        handler: function() {
                            w.close();
                            me.callMethod(response, 'remove');
                        }
                    }
                ]
            });

            w.show();

            callback(w);
        });
    },

    //temp
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
                        }, 500);
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