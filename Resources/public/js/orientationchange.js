if (Ext.isFunction(window['addEventListener'])) {
    window.addEventListener('orientationchange', function() {
        // this code is responsible for properly updating layout when page is viewed from a device which has
        // orientation sensor and gets flipped ( like you flip iPad from being positioned vertically to landscape
        // mode )
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
}