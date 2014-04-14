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
        var sm = this.workbench.getService('security_manager');

        var groupsStore = Ext.create('Modera.backend.security.toolscontribution.store.Groups', {
            autoLoad: false
        });
        groupsStore.load({
            callback: function() { // ROLE_MANAGE_PERMISSIONS

                sm.isAllowed('ROLE_MANAGE_PERMISSIONS', function(permissionsAccess) {
                    var panel = Ext.create('Modera.backend.security.toolscontribution.view.Manager', {
                        sectionName: params.section,
                        groupsStore: groupsStore,
                        hasPermissionsAccess: permissionsAccess
                    });

                    onReadyCallback(panel);
                });
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
            me.getUi().activateSection(diff.getChangedParamNewValue(me, 'section'), callback);
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
    },

    // override
    getDefaultParams: function() {
        return {
            section: 'users'
        }
    },

    // override
    attachContractListeners: function(ui) {
        var me = this;
        
        var usersList = ui.down('modera-backend-security-user-list');
        usersList.on('newrecord', function(sourceComponent) {
            me.fireEvent('handleaction', 'newuser', sourceComponent);
        });
        usersList.on('editrecord', function(sourceComponent, params) {
            me.fireEvent('handleaction', 'edituser', sourceComponent, params);
        });
        usersList.on('deleterecord', function(sourceComponent, params) {
            me.fireEvent('handleaction', 'deleteuser', sourceComponent, params);
        });
        usersList.on('editpassword', function(sourceComponent, params) {
            me.fireEvent('handleaction', 'editpassword', sourceComponent, params);
        });
        usersList.on('editgroups', function(sourceComponent, params) {
            me.fireEvent('handleaction', 'editgroups', sourceComponent, params);
        });

        var groupsOverview = ui.down('modera-backend-security-group-overview');
        groupsOverview.on('creategroup', function(sourceComponent) {
            me.fireEvent('handleaction', 'newgroup', sourceComponent);
        });
        groupsOverview.on('deletegroup', function(sourceComponent, record) {
            me.fireEvent('handleaction', 'deletegroup', sourceComponent, { id: record.get('id') });
        });
        groupsOverview.on('editgroup', function(sourceComponent, record) {
            me.fireEvent('handleaction', 'editgroup', sourceComponent, { id: record.get('id') });
        });
    }
});