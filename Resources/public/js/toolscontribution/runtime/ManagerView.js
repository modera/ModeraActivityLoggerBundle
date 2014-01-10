/**
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.runtime.ManagerView', {
    extend: 'MF.viewsmanagement.views.AbstractView',

    requires: [
    ],

    // override
    getId: function() {
        return 'manager';
    },

    // override
    isHomeView: function() {
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

        var panel = Ext.create('Modera.backend.security.toolscontribution.view.Manager', {
            sectionName: sectionName
        });

        onReadyCallback(panel);
    },

    // internal
    onSectionLoaded: function(section) {
        var me = this;
        section.relayEvents(me.getUi(), ['handleaction']);
    },

    // override
    applyTransition: function(diff, callback) {
        var me = this;

        if (diff.isViewParamValueChanged(me, 'section')) {
            me.getUi().activateSection(diff.getViewParamChangedNewValue(me, 'section'), callback);
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
    }
});