/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.backend.security.toolscontribution.controller.Groups', {
    extend: 'Ext.app.Controller',

    // override
    init: function() {
        this.callParent(arguments);

        this.control({
            'modera-backend-security-group-overview': {
                'groupselected': this.onGroupSelected
            }
        });
    },

    // private
    onGroupSelected: function(ui, record) {
        ui.showGroupUsers(record.get('id'), record.get('name'));
    }
});