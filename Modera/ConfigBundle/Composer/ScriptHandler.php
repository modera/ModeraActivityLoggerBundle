<?php

namespace Modera\ConfigBundle\Composer;

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
    public static function installConfigEntries(Event $event)
    {
        $options = static::getOptions($event);
        $appDir = $options['symfony-app-dir'];

        echo '>>> ModeraConfigBundle: Install config entries'.PHP_EOL;

        if (!is_dir($appDir)) {
            echo 'The symfony-app-dir ('.$appDir.') specified in composer.json was not found in '.getcwd().'.'.PHP_EOL;

            return;
        }

        static::executeCommand($event, $appDir, 'modera:config:install-config-entries', $options['process-timeout']);

        echo '>>> ModeraConfigBundle: done'.PHP_EOL;
    }
}
