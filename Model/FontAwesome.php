<?php

namespace Modera\MjrIntegrationBundle\Model;

use Symfony\Component\Yaml\Yaml;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class FontAwesome
{
    /**
     * @var array
     */
    private static $icons = array();

    /**
     * @return array
     */
    private static function getIconsData()
    {
        $path = dirname(__DIR__).'/Resources/config/font-awesome-icons.yml';

        return Yaml::parse(file_get_contents($path));
    }

    /**
     * @param $id
     * @param $unicode
     *
     * @return array
     */
    private static function prepareIcon($id, $unicode)
    {
        $name = strtoupper(str_replace('-', '_', $id));
        $value = 'x'.$unicode.'@FontAwesome';

        return array(
            'name' => $name,
            'value' => $value,
        );
    }

    /**
     * @return array
     */
    private static function getIcons()
    {
        if (count(self::$icons)) {
            return self::$icons;
        }

        $icons = array();
        $data = self::getIconsData();
        foreach ($data['icons'] as $icon) {
            $_icon = self::prepareIcon($icon['id'], $icon['unicode']);
            $icons[$_icon['name']] = $_icon['value'];
            if (isset($icon['aliases'])) {
                foreach ($icon['aliases'] as $alias) {
                    $_icon = self::prepareIcon($alias, $icon['unicode']);
                    $icons[$_icon['name']] = $_icon['value'];
                }
            }
        }
        self::$icons = $icons;

        return $icons;
    }

    /**
     * http://fontawesome.io/icons/.
     *
     * @param $name
     *
     * @return string|null
     */
    public static function resolve($name)
    {
        $icons = self::getIcons();
        if (false !== strrpos($name, 'fa-')) {
            $name = substr($name, 3);
        }
        $name = strtoupper(str_replace('-', '_', $name));

        if (isset($icons[$name])) {
            return $icons[$name];
        }

        return;
    }

    /**
     * @return string
     */
    public static function jsCode()
    {
        $icons = array();
        foreach (self::getIcons() as $name => $value) {
            $icons[] = $name.': \''.$value.'\'';
        }
        $iconsStr = implode(",\n    ", $icons);

        $js = <<<JS

Ext.define('FontAwesome', {
    singleton: true,

    FONT_FAMILY: 'FontAwesome',

    $iconsStr,

    resolve: function(name) {
        var me = this;
        if (name.indexOf('fa-') !== -1) {
            name = name.substr(3);
        }
        name = name.replace(/-/g, '_');
        return me[name.toUpperCase()];
    }
});

Ext.onReady(function() {
    Ext.setGlyphFontFamily(FontAwesome.FONT_FAMILY);
});

JS;

        return $js;
    }
}
