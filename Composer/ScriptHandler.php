<?php

namespace Modera\BackendModuleBundle\Composer;

use Composer\Script\Event;
use Symfony\Component\Yaml\Yaml;
use Modera\Module\Composer\ScriptHandler as ModuleScriptHandler;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ScriptHandler extends ModuleScriptHandler
{
    /**
     * @param Event $event
     * @param $value
     */
    private static function setMaintenance(Event $event, $value)
    {
        $options = static::getOptions($event);
        $path = $options['incenteev-parameters']['file'];

        $data = Yaml::parse(file_get_contents($path));
        $data['parameters']['maintenance'] = $value;

        file_put_contents($path, Yaml::dump($data));
    }

    /**
     * @param Event $event
     */
    public static function enableMaintenance(Event $event)
    {
        echo '*** Enable maintenance' . PHP_EOL;

        static::setMaintenance($event, true);
        static::clearCache($event);
    }

    /**
     * @param Event $event
     */
    public static function disableMaintenance(Event $event)
    {
        echo '*** Disable maintenance: ' . PHP_EOL;

        static::setMaintenance($event, false);
        static::clearCache($event);
    }
}