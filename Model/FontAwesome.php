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
    static private $icons = array();

    /**
     * @return array
     */
    static private function getIconsData()
    {
        $path = dirname(__DIR__) . '/Resources/config/font-awesome-icons.yml';
        return Yaml::parse(file_get_contents($path));
    }

    /**
     * @param $id
     * @param $unicode
     * @return array
     */
    static private function prepareIcon($id, $unicode)
    {
        $name = strtoupper(str_replace('-', '_', $id));
        $value = 'x' . $unicode . '@FontAwesome';
        return array(
            'name'  => $name,
            'value' => $value,
        );
    }

    /**
     * @return array
     */
    static private function getIcons()
    {
        if (count(static::$icons)) {
            return static::$icons;
        }

        $icons = array();
        $data = static::getIconsData();
        foreach ($data['icons'] as $icon) {
            $_icon = static::prepareIcon($icon['id'], $icon['unicode']);
            $icons[$_icon['name']] = $_icon['value'];
            if (isset($icon['aliases'])) {
                foreach($icon['aliases'] as $alias) {
                    $_icon = static::prepareIcon($alias, $icon['unicode']);
                    $icons[$_icon['name']] = $_icon['value'];
                }
            }
        }
        static::$icons = $icons;

        return $icons;
    }

    /**
     * http://fontawesome.io/icons/
     * @param $name
     * @return string|null
     */
    static public function resolve($name)
    {
        $icons = static::getIcons();
        if (false !== strrpos($name, 'fa-')) {
            $name = substr($name, 3);
        }
        $name = strtoupper(str_replace('-', '_', $name));

        if (isset($icons[$name])) {
            return $icons[$name];
        }

        return null;
    }

    /**
     * @return string
     */
    static public function jsCode()
    {
        $icons = array();
        foreach (static::getIcons() as $name => $value) {
            $icons[] = $name . ': \'' . $value . '\'';
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