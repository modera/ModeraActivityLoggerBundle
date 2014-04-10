/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.runtime.ManagerActivity', {
    extend: 'MF.activation.activities.AbstractActivity',

    requires: [
    ],

    // override
    getId: function() {
        return 'manager';
    },

    // override
    isHomeActivity: function() {
        return true;
    },

    // override
    init: function(executionContext) {
        this.callParent(arguments);

        executionContext.getApplication().loadController('Modera.backend.security.toolscontribution.controller.Manager');
        executionContext.getApplication().loadController('Modera.backend.security.toolscontribution.controller.Groups');
    },

    // override
    doCreateUi: function(params, onReadyCallback) {
        var sectionName = params.section || 'users';

        var groupsStore = Ext.create('Modera.backend.security.toolscontribution.store.Groups', {
            autoLoad: false
        });
        groupsStore.load({
            callback: function() {

                var panel = Ext.create('Modera.backend.security.toolscontribution.view.Manager', {
                    sectionName: sectionName,
                    groupsStore: groupsStore
                });

                onReadyCallback(panel);
            }
        });
    },

    // internal
    onSectionLoaded: function(section) {
        var me = this;
        section.relayEvents(me.getUi(), ['handleaction']);
    },

    // override
    applyTransition: function(diff, callback) {
        var me = this;

        if (diff.isParamValueChanged(me, 'section')) {
            me.getUi().activateSection(diff.getActivityParamChangedNewValue(me, 'section'), callback);
        } else {
            callback();
        }
    },

    // override
    attachStateListeners: function(ui) {
        var me = this;

        ui.on('sectionchanged', function(sourceComponent, section) {
            me.executionContext.updateParams(me, {
                section: section
            })
        });

        ui.down('modera-backend-security-permission-list').on('permissionchange', function(sourceComponent, params) {
            Actions.ModeraBackendSecurity_Permissions.update({ record: params }, function(response) {});
        });
    }
});