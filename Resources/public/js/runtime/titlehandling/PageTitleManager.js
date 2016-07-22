/**
 * Allows to manage page's title, supports dynamic parameters using a template language provided by Ext.XTemplate.
 *
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
Ext.define('Modera.mjrintegration.runtime.titlehandling.PageTitleManager', {

    requires: [
        'Ext.XTemplate'
    ],

    /**
     * @private
     * @property {MF.runtime.applications.BaseApplication} application
     */

    /**
     * @private
     * @property {String} titlePattern
     */

    /**
     * @param {Object} config
     */
    constructor: function (config) {
        Ext.apply(this, config);
    },

    /**
     * Sets page title as is, without substituting any possible dynamic parameters.
     *
     * @param {String} title
     */
    setTitle: function(title) {
        Ext.getDoc().dom.title = title;
    },

    /**
     * @return {String}
     */
    getTitlePattern: function() {
        return this.titlePattern;
    },

    /**
     * Compiles a template using a system provided title-pattern and updates page's title. You can use
     * a second argument to modify or completely replace the original title pattern.
     *
     * @param {Object} values  Optional object with values for dynamic parameters that can be used in title. If provided
     *                         the parameters provided by the system won't be lost and instead be merged (though you
     *                         can override them).
     * @param {Function} modifyPatternCallback  Optional callback that if provided will receive a title pattern
     *                                          provided by the system that you can modify or replace. Returned
     *                                          by callback pattern will be used to compile result using given "values".
     *
     * @return {String} New title
     */
    updateTitle: function(values, modifyPatternCallback) {
        var pattern = Ext.isFunction(modifyPatternCallback) ? modifyPatternCallback(this.titlePattern, this) : this.titlePattern;

        var newTitle = this.compileTitle(pattern, values);

        this.setTitle(newTitle);

        console.debug('%s.updateTitle(values=%s): set a new title "%s"', this.$className, JSON.stringify(values), newTitle);

        return newTitle;
    },

    /**
     * @private
     *
     * @param {String} pattern
     * @param {Object} values
     */
    compileTitle: function(pattern, values) {
        var me = this,
            container = me.application.getContainer();

        values = values || {};

        // if pattern contains no dynamic parameters then we automatically append :sectionNameWithDash"
        // as it should be a default behaviour
        var patternHasParams = -1 != pattern.indexOf('{');

        pattern = patternHasParams ? pattern : '{sectionNameWithDash}'+pattern;

        var tpl = Ext.create('Ext.XTemplate',
            pattern,
            // these are the functions that can be used inside a pattern, it even can go to such as extremes as:
            // Welcome, {[this.getService('security_context').getUser().email]}.
            {
                getCurrentSection: function() {
                    return container.get('workbench').getCurrentSection();
                },
                getWorkbench: function() {
                    return container.get('workbench');
                },
                getApp: function() {
                    return me.application;
                },
                getService: function(id) {
                    return container.get(id);
                }
            }
        );

        var currentSection = this.application.getContainer().get('workbench').getCurrentSection(),
            sectionName = Ext.isFunction(currentSection['getTitle']) ? currentSection.getTitle() : '';

        var defaultValues = {
            sectionName: sectionName,
            sectionNameWithDash: !sectionName ? sectionName : sectionName+' - '
        };

        values = Ext.apply(defaultValues, values);

        return tpl.apply(values);
    }
});