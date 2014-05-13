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
//                new InputOption('versions-path', null, InputOption::VALUE_OPTIONAL, 'versions.json path', getcwd() . '/versions.json'),
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

//        $versionsPathOption = $input->getOption('versions-path');
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

        if ($dependenciesOption) {

            $newVersion = null;
            $versions = array_keys($currentVersionsFileContents);
            $currentVersion = isset($currentComposerFileContents['extra']['modera-version']) ? $currentComposerFileContents['extra']['modera-version'] : null;

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

            $oldDependencies = $newDependencies = array();
            if (!$currentVersion) {
                $newVersion = array_keys($currentVersionsFileContents)[0];
                $newDependencies = array_values($currentVersionsFileContents)[0]['dependencies'];
            } else {
                foreach(array_keys($versions) as $k) {
                    if ($versions[$k] == $currentVersion) {
                        $newVersion = $versions[$k + 1];

                        $oldDependencies = isset($currentVersionsFileContents[$currentVersion]['dependencies'])
                                         ? $currentVersionsFileContents[$currentVersion]['dependencies'] :
                                           array();

                        $newDependencies = isset($currentVersionsFileContents[$newVersion]['dependencies'])
                                         ? $currentVersionsFileContents[$newVersion]['dependencies']
                                         : $currentVersionsFileContents[$currentVersion]['dependencies'];
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
                if (!isset($dependenciesOption[$name]) || $oldDependencies[$name] == $dependenciesOption[$name] || $ver == $dependenciesOption[$name]) {
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

            $repositories = isset($currentComposerFileContents['repositories']) ? $currentComposerFileContents['repositories'] : array();
            if (isset($currentVersionsFileContents[$newVersion]['add-repositories'])) {
                foreach ($currentVersionsFileContents[$newVersion]['add-repositories'] as $repo) {
                    if (false === array_search($repo, $repositories)) {
                        $repositories[] = $repo;
                    }
                }
            }
            if (isset($currentVersionsFileContents[$newVersion]['rm-repositories'])) {
                foreach ($currentVersionsFileContents[$newVersion]['rm-repositories'] as $repo) {
                    if (false !== ($key = array_search($repo, $repositories))) {
                        unset($repositories[$key]);
                    }
                }
                $repositories = array_values($repositories);
            }
            $currentComposerFileContents['repositories'] = $repositories;

            $composerFile->write($currentComposerFileContents);

            $output->writeln("<info>composer.json 'requires' section has been updated to version $newVersion</info>");

            if (isset($currentVersionsFileContents[$newVersion]['add-bundles']) && count($currentVersionsFileContents[$newVersion]['add-bundles'])) {
                $output->writeln("<comment>Add bundle(s) to app/AppKernel.php</comment>");
                foreach ($currentVersionsFileContents[$newVersion]['add-bundles'] as $bundle) {
                    $output->writeln('    ' . $bundle);
                }
            }
            if (isset($currentVersionsFileContents[$newVersion]['rm-bundles']) && count($currentVersionsFileContents[$newVersion]['rm-bundles'])) {
                $output->writeln("<comment>Remove bundle(s) from app/AppKernel.php</comment>");
                foreach ($currentVersionsFileContents[$newVersion]['rm-bundles'] as $bundle) {
                    $output->writeln('    ' . $bundle);
                }
            }

            if (isset($currentVersionsFileContents[$newVersion]['commands']) && count($currentVersionsFileContents[$newVersion]['commands'])) {
                $output->writeln('After composer update run:');
                $output->writeln('<info>php app/console ' . $this->getName() . ' --run-commands</info>');
            }

        } else if ($runCommandsOption) {

            $extra = isset($currentComposerFileContents['extra']) ? $currentComposerFileContents['extra'] : array();
            if (isset($extra['modera-version']) && isset($currentVersionsFileContents[$extra['modera-version']])) {
                $versionData = $currentVersionsFileContents[$currentComposerFileContents['extra']['modera-version']];
                $commands = isset($versionData['commands']) ? $versionData['commands'] : array();

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
