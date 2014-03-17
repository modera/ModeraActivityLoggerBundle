Ext.define('X.Application', {
    extend: 'MF.runtime.applications.authenticationaware.AuthenticationRequiredApplication',

    name: 'X',

    requires: [
        'MF.Util',
        'MF.runtime.applications.authenticationaware.controller.Workbench'
    ],

    // override
    modifyContainerServiceDefinitions: function(services) {
        var newServices = {
            "security_manager": {
                "className": "MF.security.AjaxSecurityManager",
                "args": [
                    {
                        "urls": {
                            "login": "\/app_dev.php\/login_check",
                            "isAuthenticated": "\/app_dev.php\/is-authenticated",
                            "logout": "\/app_dev.php\/logout"
                        },
                        "configProvider": "@config_provider"
                    }
                ]
            },
            "config_provider": {
                "className": "MF.runtime.config.AjaxConfigProvider",
                "args": [
                    {
                        "url": "get-config"
                    }
                ]
            }
        };

        return Ext.Object.merge(services, newServices);
    }
});