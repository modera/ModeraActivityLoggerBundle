/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.module.toolscontribution.runtime.ModuleDetailsWindowView', {
    extend: 'MF.viewsmanagement.views.AbstractView',

    requires: [
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
                title: response.name,
                width: 900,
                height: 400,
                modal: true,
                items: {
                    xtype: 'form',
                    bodyPadding: 20,
                    items: items
                },
                bbar: [
                    {
                        disabled: (response.installed && response.lastVersion == response.currentVersion),
                        xtype: 'button',
                        text: (response.installed && response.lastVersion != response.currentVersion ? 'Update' : 'Install'),
                        handler: function() {
                            w.close();
                            me.callMethod(params.id, 'require');
                        }
                    },
                    '->',
                    {
                        disabled: !response.installed,
                        xtype: 'button',
                        text: 'Remove',
                        handler: function() {
                            w.close();
                            me.callMethod(params.id, 'remove');
                        }
                    }
                ]
            });

            w.show();

            callback(w);
        });
    },

    //temp
    callMethod: function(id, method) {
        Actions.ModeraBackendModule_Default[method]({ id: id }, function(response) {
            var title = 'Error';
            if (response.success) {
                title = method + ': ' + response.status['name'];
                if ('require' == method) {
                    title += ':' + response.status['version'];
                }
            }

            var w = Ext.create('Ext.window.Window', {
                title: title,
                width: 500,
                height: 400,
                modal: true,
                items: {
                    xtype: 'panel',
                    bodyPadding: 20,
                    items: {
                        itemId: 'status',
                        html: response.msg
                    }
                }
            });
            w.show();

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