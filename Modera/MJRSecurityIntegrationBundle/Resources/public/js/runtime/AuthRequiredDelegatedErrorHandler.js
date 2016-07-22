/**
 * MPFE-817
 *
 * If server-response resembles to an error when user's authentication session has expired then this handler
 * will show a window informing a user about this and offer an option to login again.
 *
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.mjrsecurityintegration.runtime.AuthRequiredDelegatedErrorHandler', {
    extend: 'MF.runtime.servererrorhandling.AbstractDelegatedErrorHandler',

    requires: [
        'MF.Util'
    ],

    // l10n
    errorText: "Your session has expired and you need to re-login or you don't have privileges to perform given action.",
    loginText: 'Login',

    /**
     * @private
     * @property {MFC.window.ModalWindow} window
     */

    /**
     * @param {Object} config
     */
    constructor: function(config) {
        MF.Util.validateRequiredConfigParams(this, config, ['securityMgr', 'exceptionClass']);

        Ext.apply(this, config);
    },

    /**
     * @inheritDoc
     */
    handleServerError: function(error, responseStatusCode, callback) {
        var me = this;

        var isDirect = Ext.isObject(error) && error.hasOwnProperty('type') && 'exception' == error.type && this.exceptionClass == error['class'],
            isNative = Ext.isObject(error) && error.hasOwnProperty('success') && false == error.success && 403 == responseStatusCode,
            canHandle = isDirect || isNative;

        if (canHandle) {
            if (!this.window) {
                this.window = Ext.create('MFC.window.ModalWindow', {
                    tid: 'authRequiredWindow',
                    header: false,
                    resizable: false,
                    closeAction: 'destroy',
                    width: 600,
                    listeners: {
                        close: function() {
                            delete me['window'];
                        }
                    },
                    layout: {
                        type: 'hbox',
                        align: 'middle'
                    },
                    items: [
                        {
                            xtype: 'mfc-message',
                            scale: 'large',
                            type: 'warning',
                            width: 70
                        },
                        {
                            flex: 1,
                            items: {
                                html: '<div style="padding-top: 2px; !important;"></div><h2 class="text">'+this.errorText+'</h2>'
                            }
                        }
                    ],
                    actions: [
                        {
                            text: this.loginText,
                            tid: 'loginBtn',
                            scale: 'medium',
                            glyph: 'xe810@mf-theme-header-icon',
                            cls: 'mf-theme-login-btn',
                            handler: function() {
                                // By manually forcing a logout before a page reload we are making sure that
                                // a login screen is displayed at all times, even when this dialog has been shown
                                // when a user doesn't have a required privileges but his session still was active
                                me.securityMgr.logout(function() {
                                    window.location.reload();
                                });
                            }
                        }
                    ]
                });

                // this is an alternative solution how to create a similar window
                // but using "more extjs way", but still with a hack
                //this.window = Ext.create('MFC.window.ModalWindow', {
                //    layout: 'fit',
                //    header: false,
                //    resizable: false,
                //    bodyPadding: '10 20',
                //    items:  {
                //        width: 650,
                //        xtype: 'mfc-message',
                //        scale: 'large',
                //        type: 'warning',
                //        text: {
                //            margin: '0 0 0 10',
                //            flex: 999,
                //            html: this.errorText
                //        }
                //    }
                //});
            }
            this.window.show();
        }

        callback(canHandle);
    }
});
