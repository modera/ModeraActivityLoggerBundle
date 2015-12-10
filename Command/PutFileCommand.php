<?php

namespace Modera\FileRepositoryBundle\Command;

use Modera\FileRepositoryBundle\Repository\FileRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class PutFileCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('modera:file-repository:put-file')
            ->setDescription('Puts a file to a repository')
            ->addArgument('repository', InputArgument::REQUIRED)
            ->addArgument('local_path', InputArgument::REQUIRED)
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var FileRepository $fr */
        $fr = $this->getContainer()->get('modera_file_repository.repository.file_repository');

        $repository = $fr->getRepository($input->getArgument('repository'));
        if (!$repository) {
            throw new \RuntimeException(sprintf(
                'Unable to find a repository with name "%s"', $input->getArgument('repository')
            ));
        }

        $localPath = $input->getArgument('local_path');
        if (!file_exists($localPath) || !is_readable($localPath)) {
            throw new \RuntimeException(sprintf('Unable to find a file "%s" or it is not readable', $localPath));
        }

        $output->writeln(sprintf('Uploading "%s" to repository "%s"', $localPath, $repository->getName()));

        $storedFile = $fr->put($repository->getName(), new \SplFileInfo($localPath));

        $output->writeln(sprintf('<info>Done! File id: %d</info>', $storedFile->getId()));
    }
}
