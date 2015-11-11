<?php

namespace Modera\MjrIntegrationBundle\Command;

use Modera\MjrIntegrationBundle\DependencyInjection\ModeraMjrIntegrationExtension;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class CheckVersionCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('modera:mjr-integration:check-version')
            ->addArgument('required-version', InputArgument::REQUIRED)
            ->setDescription('Command validated that specified version of MJR is currently installed')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->getContainer()->getParameter(ModeraMjrIntegrationExtension::CONFIG_KEY);

        $mjrPath = implode(DIRECTORY_SEPARATOR, array(
            getcwd(), 'web', substr($config['runtime_path'], 1),
        ));

        $path = $mjrPath.DIRECTORY_SEPARATOR.'package.json';

        $packageJson = file_get_contents($path);
        if (false === $packageJson) {
            throw new \RuntimeException('Unable to find file '.$path);
        }

        $packageJson = json_decode($packageJson, true);

        $currentVersion = $packageJson['version'];
        $requiredVersion = trim($input->getArgument('required-version'));
        if ($currentVersion != $requiredVersion) {
            $output->writeln(
                "<comment>You have old '$currentVersion' version of MJR, downloading a required '$requiredVersion' version.</comment>"
            );

            $url = 'http://mjr.dev.modera.org/releases/mjr.tar.gz';

            $archive = file_get_contents($url);
            if (false === $archive) {
                throw new \RuntimeException("Unable to download MJR from $url");
            }

            $downloadedMjrPath = getcwd().DIRECTORY_SEPARATOR.'mjr-'.$requiredVersion.'.tar.gz';

            file_put_contents($downloadedMjrPath, $archive);

            $output->writeln(sprintf(
                'New version of MJR has been downloaded, local path: <info>%s</info> . Please extract it to <info>%s</info>',
                $downloadedMjrPath, $mjrPath
            ));
        } else {
            $output->writeln('<info>You have latest version of MJR, no need to update it.<info>');
        }
    }
}
