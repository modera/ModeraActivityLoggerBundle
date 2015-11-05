<?php

namespace Modera\SecurityBundle\Composer;

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
    public static function installPermissions(Event $event)
    {
        $options = static::getOptions($event);
        $appDir = $options['symfony-app-dir'];

        echo '>>> ModeraSecurityBundle: Install permissions'.PHP_EOL;

        if (!is_dir($appDir)) {
            echo 'The symfony-app-dir ('.$appDir.') specified in composer.json was not found in '.getcwd().', can not install permissions.'.PHP_EOL;

            return;
        }

        static::executeCommand($event, $appDir, 'modera:security:install-permission-categories', $options['process-timeout']);
        static::executeCommand($event, $appDir, 'modera:security:install-permissions', $options['process-timeout']);

        echo '>>> ModeraSecurityBundle: done'.PHP_EOL;
    }
}
