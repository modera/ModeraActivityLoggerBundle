<?php

namespace Modera\FileRepositoryBundle\Command;

use Modera\FileRepositoryBundle\Repository\FileRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class CreateRepositoryCommand extends ContainerAwareCommand
{
    // override
    protected function configure()
    {
        $this
            ->setName('modera:file-repository:create')
            ->setDescription('Command allows to create a file repository')
            ->addArgument('name', InputArgument::REQUIRED)
            ->addArgument('filesystem', InputArgument::REQUIRED)
            ->addArgument('label', InputArgument::OPTIONAL)
            ->addOption('overwrite-files', 'o', InputOption::VALUE_NONE, 'Overwrite files with same name')
        ;
    }

    // override
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var FileRepository $fr */
        $fr = $this->getContainer()->get('modera_file_repository.repository.file_repository');

        $config = array(
            'filesystem'      => $input->getArgument('filesystem'),
            'overwrite_files' => $input->getOption('overwrite-files'),
        );

        $repository = $fr->createRepository($input->getArgument('name'), $config, $input->getArgument('label'));

        $output->writeln("Repository has been successfully created! Its internal is #" . $repository->getId());
    }
} 