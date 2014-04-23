/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.configutils.view.WidgetsPoolEditorColumn', {
    extend: 'Ext.grid.column.Column',
    alias: 'widget.modera-configutils-widgetspooleditorcolumn',

    requires: [
        'MF.Util'
    ],

    /**
     * @cfg {Object} editorsPool
     */

    /**
     * @param {Object} config
     */
    constructor: function(config) {
        MF.Util.validateRequiredConfigParams(this, config, ['editorsPool']);

        config = Ext.apply(config, {
            getEditor: this.getEditor
        });

        this.callParent([config]);
    },

    // private
    getEditor: function(record) {
        if (record.get('isReadOnly')) {
            return false;
        }

        var name = record.get('name');

        if (this.editorsPool[name]) {
            var editor = this.editorsPool[name];

            if (Ext.isFunction(editor)) {
                return editor(record);
            } else { // widget definition or widget instance
                return editor;
            }
        }

        return false;
    }
});