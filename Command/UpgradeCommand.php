<?php

namespace Modera\UpgradeBundle\Command;

use Modera\UpgradeBundle\Json\JsonFile;
use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class UpgradeCommand extends ContainerAwareCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setName('modera:upgrade')
            ->setDescription('Updates dependencies in "composer.json" and runs required commands to upgrade MF')
            ->setDefinition([
                new InputOption('dependencies', null, InputOption::VALUE_NONE, 'Update dependencies in "composer.json"'),
                new InputOption('run-commands', null, InputOption::VALUE_NONE, 'Run commands'),
                new InputArgument('versions-path', InputArgument::OPTIONAL, 'versions.json path', getcwd() . '/versions.json')
            ])
        ;
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var DialogHelper $dialog */
        $dialog = $this->getHelperSet()->get('dialog');

        $dependenciesOption = $input->getOption('dependencies');
        $runCommandsOption = $input->getOption('run-commands');

        $versionsPathArg = $input->getArgument('versions-path');

        $output->writeln('');

        if (!$dependenciesOption && !$runCommandsOption) {
            $msg = [
                'If you want to update dependencies then please use <info>--dependencies</info> option for the command, ',
                'if you need to have commands executed when a version is upgraded then use <info>--run-commands</info> option instead.'
            ];
            $output->writeln(implode('', $msg));
            $output->writeln('');

            return;
        }

        $output->writeln("Reading upgrade instructions from '<info>$versionsPathArg</info>'.");

        $basePath = dirname($this->getContainer()->get('kernel')->getRootdir());
        $composerFile = new JsonFile($basePath . '/composer.json');
        $versionsFile = new JsonFile($versionsPathArg);

        $currentComposerFileContents = $composerFile->read();
        $currentVersionsFileContents = $versionsFile->read();

        $composerExtraContents = $this->getArrayValue(
            $currentComposerFileContents, 'extra', array()
        );

        if ($dependenciesOption) {

            $newVersion = null;
            $versions = array_keys($currentVersionsFileContents);
            $currentVersion = $this->getArrayValue(
                $composerExtraContents, 'modera-version'
            );

            if ($currentVersion && $currentVersion == $versions[count($versions) - 1]) {
                $output->writeln("<info>You have the latest version $currentVersion</info>");
                $output->writeln('');

                return;
            }

            // backup composer.json
            file_put_contents(
                $basePath . '/composer' . ($currentVersion ? '.v' . $currentVersion : '') . '.backup.json',
                file_get_contents($basePath . '/composer.json')
            );

            // manage dependencies
            $oldDependencies = $newDependencies = array();
            if (!$currentVersion) {
                $newVersion = $versions[0];
                $newDependencies = $this->getArrayValue(
                    $currentVersionsFileContents[$newVersion], 'dependencies', array()
                );
            } else {
                foreach(array_keys($versions) as $k) {
                    if ($versions[$k] == $currentVersion) {
                        $key = $k;
                        while ($key >= 0) {
                            $oldDependencies = $this->getArrayValue(
                                $currentVersionsFileContents[$versions[$key]], 'dependencies'
                            );
                            if (is_array($oldDependencies)) {
                                break;
                            }
                            $oldDependencies = array();
                            --$key;
                        }

                        $newVersion = $versions[$k + 1];
                        $newDependencies = $this->getArrayValue(
                            $currentVersionsFileContents[$newVersion], 'dependencies', $oldDependencies
                        );
                        break;
                    }
                }
            }
            $dependenciesDiff = $this->diffDependencies($oldDependencies, $newDependencies);
            $output->writeln(sprintf('<info>Upgrading from %s to %s</info>', $currentVersion ?: '-', $newVersion));

            $dependenciesOption = $currentComposerFileContents['require'];
            foreach ($dependenciesDiff['added'] as $name => $ver) {
                if (!isset($dependenciesOption[$name])) {
                    $dependenciesOption[$name] = $ver;
                } else {
                    if ($ver !== $dependenciesOption[$name]) {
                        $msg = sprintf(implode('', [
                            '<question>',
                                'Dependency "%s:%s" already exists. ',
                                'Would you like to change it to "%s:%s"? (Y/n)',
                            '</question>',
                        ]), $name, $dependenciesOption[$name], $name, $ver);

                        if ($dialog->askConfirmation($output, $msg)) {
                            $dependenciesOption[$name] = $ver;
                        }
                    }
                }
            }
            foreach ($dependenciesDiff['changed'] as $name => $ver) {
                if (
                       !isset($dependenciesOption[$name])
                    || $oldDependencies[$name] == $dependenciesOption[$name]
                    || $ver == $dependenciesOption[$name]
                ) {
                    $dependenciesOption[$name] = $ver;
                } else {
                    $msg = sprintf(implode('', [
                        '<question>',
                            'Dependency "%s:%s" already changed. ',
                            'Would you like to change it to "%s:%s"? (Y/n)',
                        '</question>',
                    ]), $name, $dependenciesOption[$name], $name, $ver);

                    if ($dialog->askConfirmation($output, $msg)) {
                        $dependenciesOption[$name] = $ver;
                    }
                }
            }
            foreach ($dependenciesDiff['removed'] as $name => $ver) {
                if (isset($dependenciesOption[$name])) {
                    $msg = sprintf(implode('', [
                        '<question>',
                            'Dependency "%s" has been removed. ',
                            'Would you like to remove it? (Y/n)',
                        '</question>',
                    ]), $name);

                    if ($dialog->askConfirmation($output, $msg)) {
                        unset($dependenciesOption[$name]);
                    }
                }
            }
            foreach ($dependenciesDiff['same'] as $name => $ver) {
                if (!isset($dependenciesOption[$name])) {
                    $dependenciesOption[$name] = $ver;
                } else if ($ver !== $dependenciesOption[$name]) {
                    $msg = sprintf(implode('', [
                        '<question>',
                            'Dependency "%s:%s" has been manually changed. ',
                            'Would you like to restore it to "%s:%s"? (Y/n)',
                        '</question>',
                    ]), $name, $dependenciesOption[$name], $name, $ver);

                    if ($dialog->askConfirmation($output, $msg)) {
                        $dependenciesOption[$name] = $ver;
                    }
                }
            }
            $currentComposerFileContents['require'] = $dependenciesOption;
            $currentComposerFileContents['extra']['modera-version'] = $newVersion;

            // manage repositories
            $repositories = $this->getArrayValue(
                $currentComposerFileContents, 'repositories', array()
            );
            $addRepositories = $this->getArrayValue(
                $currentVersionsFileContents[$newVersion], 'add-repositories'
            );
            $rmRepositories = $this->getArrayValue(
                $currentVersionsFileContents[$newVersion], 'rm-repositories'
            );
            if ($addRepositories) {
                foreach ($addRepositories as $repo) {
                    if (false === array_search($repo, $repositories)) {
                        $repositories[] = $repo;
                    }
                }
            }
            if ($rmRepositories) {
                foreach ($rmRepositories as $repo) {
                    if (false !== ($key = array_search($repo, $repositories))) {
                        unset($repositories[$key]);
                    }
                }
                $repositories = array_values($repositories);
            }
            $currentComposerFileContents['repositories'] = $repositories;

            // write composer.json
            $composerFile->write($currentComposerFileContents);

            // interactions
            $output->writeln("<info>composer.json 'requires' section has been updated to version $newVersion</info>");

            if (count($this->getArrayValue($currentVersionsFileContents[$newVersion], 'add-bundles'))) {
                $output->writeln("<comment>Add bundle(s) to app/AppKernel.php</comment>");
                foreach ($currentVersionsFileContents[$newVersion]['add-bundles'] as $bundle) {
                    $output->writeln('    ' . $bundle);
                }
            }
            if (count($this->getArrayValue($currentVersionsFileContents[$newVersion], 'rm-bundles'))) {
                $output->writeln("<comment>Remove bundle(s) from app/AppKernel.php</comment>");
                foreach ($currentVersionsFileContents[$newVersion]['rm-bundles'] as $bundle) {
                    $output->writeln('    ' . $bundle);
                }
            }

            if (count($this->getArrayValue($currentVersionsFileContents[$newVersion], 'instructions'))) {
                foreach ($currentVersionsFileContents[$newVersion]['instructions'] as $instruction) {
                    $output->writeln(sprintf('<comment>%s</comment>', $instruction));
                }
            }

            if (count($this->getArrayValue($currentVersionsFileContents[$newVersion], 'commands'))) {
                $output->writeln('After composer update run:');
                $output->writeln('<info>php app/console ' . $this->getName() . ' --run-commands</info>');
            }

        } else if ($runCommandsOption) {

            $moderaVersion = $this->getArrayValue(
                $composerExtraContents, 'modera-version'
            );
            if ($moderaVersion) {
                $versionData = $this->getArrayValue(
                    $currentVersionsFileContents, $moderaVersion, array()
                );
                $commands = $this->getArrayValue(
                    $versionData, 'commands', array()
                );

                $this->getApplication()->setAutoExit(false);
                foreach ($commands as $command) {
                    $output->writeln('');
                    $output->writeln("<comment>$command</comment>");
                    $this->getApplication()->run(new StringInput($command), $output);
                }

                if (count($commands) == 0) {
                    $output->writeln('<comment>No commands need to be run! Aborting ...</comment>');
                }
            }

        }

        $output->writeln('');
    }

    /**
     * @param array $arr
     * @param $key
     * @param null $default
     * @return mixed
     */
    private function getArrayValue(array $arr, $key, $default = null)
    {
        if (isset($arr[$key])) {
            return $arr[$key];
        }

        return $default;
    }

    /**
     * @param array $arr1
     * @param array $arr2
     * @return array
     */
    private function diffDependencies(array $arr1, array $arr2)
    {
        $diff1   = array_diff_assoc($arr1, $arr2);
        $diff2   = array_diff_assoc($arr2, $arr1);
        $added   = array_diff_key($diff2, $diff1);
        $changed = array_diff_key($diff2, $added);
        $removed = array_diff_key($diff1, $diff2);
        $same    = array_diff_key($arr1, $diff1);

        return array(
            'added'   => $added,
            'changed' => $changed,
            'removed' => $removed,
            'same'    => $same,
        );
    }
}
