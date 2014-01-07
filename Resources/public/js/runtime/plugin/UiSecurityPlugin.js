/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.securityawarejsruntime.runtime.plugin.UiSecurityPlugin', {
    extend: 'MF.runtime.extensibility.AbstractPlugin',

    requires: [
        'Ext.tip.ToolTip'
    ],

    // private
    attachReportingListener: function(component, roleName) {
        var me = this;

        component.getEl().on('mouseenter', function() {
            console.debug(
                '%s: Required security role for this component: ' + roleName,
                me.$className
            );
        });
    },

    // private
    init: function() {
        this.callParent(arguments);

        var me = this;

        var sm = this.application.getContainer().get('security_manager');

        this.control({
            'component[handleSecurity]': {
                render: function(component) {
                    if (Ext.isFunction(component.handleSecurity)) {
                        component.handleSecurity(sm, me.application);
                    }
                }
            },
            'component[securityRole]': {
                render: function(component) {
                    if (Ext.isString(component.securityRole)) {
                        me.attachReportingListener(component, component.securityRole);

                        sm.isAllowed(component.securityRole, function(isAllowed) {
                            if (!isAllowed) {
                                component.disable();
                            }
                        });
                    }
                }
            },
            'component[security]': {
                render: function(component) {
                    if (Ext.isObject(component.security) && component.security.role) {
                        me.attachReportingListener(component, component.security.role);

                        var strategy = Ext.isString(component.security.strategy) ? component.security.strategy : 'disable';

                        sm.isAllowed(component.security.role, function(isAllowed) {
                            if (!isAllowed) {
                                if ('disable' == strategy) {
                                    component.disable();
                                } else if ('remove' == strategy) {
                                    if (component.ownerCt) {
                                        component.ownerCt.remove(component);
                                    }
                                } else if ('hide' == strategy) {
                                    component.hide();
                                }
                            }
                        });
                    }
                }
            }
        });
    },

    // override
    bootstrap: function(callback) {
        callback();
    }
});