<?php

namespace Modera\ModuleBundle\Composer;

use Composer\Composer;
use Composer\Script\Event;
use Composer\Script\PackageEvent;
use Composer\DependencyResolver\Operation\UpdateOperation;
use Symfony\Component\Yaml\Yaml;
use Modera\Module\Service\ComposerService;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2013 Modera Foundation
 */
class ScriptHandler extends AbstractScriptHandler
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
        echo '*** Enable maintenance'.PHP_EOL;

        try {
            static::setMaintenance($event, true);
            static::clearCache($event);
        } catch (\RuntimeException $e) {
            echo $e->getMessage().PHP_EOL;
        }
    }

    /**
     * @param Event $event
     */
    public static function disableMaintenance(Event $event)
    {
        echo '*** Disable maintenance'.PHP_EOL;

        try {
            static::setMaintenance($event, false);
            static::clearCache($event);
        } catch (\RuntimeException $e) {
            echo $e->getMessage().PHP_EOL;
        }
    }

    /**
     * @param Event $event
     */
    public static function eventDispatcher(Event $event)
    {
        static $_scripts = array();

        if ($event instanceof PackageEvent) {
            $operation = $event->getOperation();
            if ($operation instanceof UpdateOperation) {
                $package = $operation->getTargetPackage();
            } else {
                $package = $operation->getPackage();
            }

            $options = ComposerService::getOptions($event->getComposer());
            if ($package->getType() != $options['type']) {
                //return;
            }

            $extra = $package->getExtra();
            $delayedEvents = array('post-package-install', 'post-package-update');

            if (is_array($extra) && isset($extra[$options['type']])) {
                if (isset($extra[$options['type']]['scripts'])) {
                    if (isset($extra[$options['type']]['scripts'][$event->getName()])) {
                        $scripts = $extra[$options['type']]['scripts'][$event->getName()];
                        if (!is_array($scripts)) {
                            $scripts = array($scripts);
                        }

                        foreach ($scripts as $script) {
                            if (in_array($event->getName(), $delayedEvents)) {
                                $_scripts[$event->getName()][] = array(
                                    'script' => $script,
                                    'event' => $event,
                                );
                            } elseif (is_callable($script)) {
                                $className = substr($script, 0, strpos($script, '::'));
                                $methodName = substr($script, strpos($script, '::') + 2);
                                $className::$methodName($event);
                            }
                        }
                    }
                }
            }
        } elseif (in_array($event->getName(), array('post-install-cmd', 'post-update-cmd'))) {
            foreach ($_scripts as $eventName => $scripts) {
                foreach ($scripts as $data) {
                    if (is_callable($data['script'])) {
                        $className = substr($data['script'], 0, strpos($data['script'], '::'));
                        $methodName = substr($data['script'], strpos($data['script'], '::') + 2);
                        $className::$methodName($data['event']);
                    }
                }
            }
        }
    }

    /**
     * @param Event $event
     */
    public static function registerBundles(Event $event)
    {
        $options = static::getOptions($event);
        $appDir = $options['symfony-app-dir'];

        if (!is_dir($appDir)) {
            echo 'The symfony-app-dir ('.$appDir.') specified in composer.json was not found in '.getcwd().'.'.PHP_EOL;

            return;
        }

        $bundlesFile = 'AppModuleBundles.php';
        $bundles = ComposerService::getRegisterBundles($event->getComposer());

        static::createRegisterBundlesFile($bundles, $appDir.'/'.$bundlesFile);
        static::executeCommand($event, $appDir, 'modera:module:register '.$bundlesFile, $options['process-timeout']);
    }

    /**
     * @param array $bundles
     * @param $outputFile
     */
    protected static function createRegisterBundlesFile(array $bundles, $outputFile)
    {
        $data = array('<?php return array(');
        foreach ($bundles as $bundleClassName) {
            $data[] = '    new '.$bundleClassName.'(),';
        }
        $data[] = ');';

        $fs = new Filesystem();
        $fs->dumpFile($outputFile, implode("\n", $data)."\n");

        if (!$fs->exists($outputFile)) {
            throw new \RuntimeException(sprintf('The "%s" file must be created.', $outputFile));
        }
    }

    /**
     * Clears the Symfony cache.
     *
     * @param $event Event A instance
     */
    public static function clearCache(Event $event)
    {
        $options = static::getOptions($event);
        $appDir = $options['symfony-app-dir'];

        if (!is_dir($appDir)) {
            echo 'The symfony-app-dir ('.$appDir.') specified in composer.json was not found in '.getcwd().'.'.PHP_EOL;

            return;
        }

        static::executeCommand($event, $appDir, 'cache:clear --env=prod --no-warmup --quiet', $options['process-timeout']);
    }

    /**
     * Executes the SQL needed to update the database schema to match the current mapping metadata.
     *
     * @param $event Event A instance
     */
    public static function doctrineSchemaUpdate(Event $event)
    {
        $options = static::getOptions($event);
        $appDir = $options['symfony-app-dir'];

        if (!is_dir($appDir)) {
            echo 'The symfony-app-dir ('.$appDir.') specified in composer.json was not found in '.getcwd().'.'.PHP_EOL;

            return;
        }

        static::executeCommand($event, $appDir, 'doctrine:schema:update --force', $options['process-timeout']);
    }

    /**
     * Creates the configured databases and executes the SQL needed to update the database schema, if database not created.
     *
     * @param Event $event
     */
    public static function initDatabase(Event $event)
    {
        $options = static::getOptions($event);
        $appDir = $options['symfony-app-dir'];

        if (!is_dir($appDir)) {
            echo 'The symfony-app-dir ('.$appDir.') specified in composer.json was not found in '.getcwd().'.'.PHP_EOL;

            return;
        }

        try {
            static::executeCommand($event, $appDir, 'doctrine:database:create --quiet', $options['process-timeout']);
            static::doctrineSchemaUpdate($event);
        } catch (\RuntimeException $e) {
        }
    }
}
