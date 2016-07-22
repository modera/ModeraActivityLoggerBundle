<?php

namespace Modera\LanguagesBundle\Composer;

use Composer\Script\Event;
use Modera\ModuleBundle\Composer\AbstractScriptHandler;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2015 Modera Foundation
 */
class ScriptHandler extends AbstractScriptHandler
{
    /**
     * @param Event $event
     */
    public static function configSync(Event $event)
    {
        $options = static::getOptions($event);
        $appDir = $options['symfony-app-dir'];

        echo '>>> ModeraLanguagesBundle: Config sync' . PHP_EOL;

        if (!is_dir($appDir)) {
            echo 'The symfony-app-dir (' . $appDir . ') specified in composer.json was not found in ' . getcwd() . '.' . PHP_EOL;
            return;
        }

        static::executeCommand($event, $appDir, 'modera:languages:config-sync', $options['process-timeout']);

        echo '>>> ModeraLanguagesBundle: done' . PHP_EOL;
    }
}