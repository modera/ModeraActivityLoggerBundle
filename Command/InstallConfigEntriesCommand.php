<?php

namespace Modera\ConfigBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class InstallConfigEntriesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('modera:config:install-config-entries')
            ->setDescription('Installs configuration-entries defined through extension-points mechanism')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(' >> Installing configuration-entries ...');

        /* @var \Modera\ConfigBundle\Config\ConfigEntriesInstaller $installer */
        $installer = $this->getContainer()->get('modera_config.config_entries_installer');
        $installedEntries = $installer->install();

        foreach ($installedEntries as $entry) {
            $output->writeln(sprintf('  - %s ( %s )', $entry->getName(), $entry->getReadableName()));
        }
        if (count($installedEntries) == 0) {
            $output->writeln(" >> There's nothing to install, aborting");
        } else {
            $output->writeln(' >> Done!');
        }
    }
}
