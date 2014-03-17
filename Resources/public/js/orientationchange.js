// this code is responsible for properly updating layout when page is viewed from a device which has
// orientation sensor and gets flipped ( like you flip iPad from being position vertically to landscape
// mode )
window.addEventListener('orientationchange', function() {
    var viewport = Ext.ComponentQuery.query('viewport')[0];
    if (viewport) {
        viewport.doLayout();
    }

    Ext.each(Ext.ComponentQuery.query('window'), function(w) {
        if (w.isVisible()) {
            w.doLayout();
            w.center();
        }
    });
});