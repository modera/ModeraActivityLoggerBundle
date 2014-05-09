<?php

namespace Modera\FileRepositoryBundle\Command;

use Modera\FileRepositoryBundle\Repository\FileRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ListFilesCommand extends ContainerAwareCommand
{
    // override
    protected function configure()
    {
        $this
            ->setName('modera:file-repository:list-files')
            ->setDescription('Allows to see files in a repository')
            ->addArgument('repository-name', InputArgument::REQUIRED)
        ;
    }

    private function formatFileSize($size)
    {
        $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $power = $size > 0 ? floor(log($size, 1024)) : 0;

        return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }

    // override
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var FileRepository $fr */
        $fr = $this->getContainer()->get('modera_file_repository.repository.file_repository');

        $repositoryName = $input->getArgument('repository-name');
        $repository = $fr->getRepository($repositoryName);

        if (!$repository) {
            throw new \RuntimeException(sprintf('Unable to find a repository with given name "%s"!', $repositoryName));
        }

        $rows = array();
        foreach ($repository->getFiles() as $storedFile) {
            $rows[] = array(
                $storedFile->getId(),
                $storedFile->getFilename(),
                $storedFile->getMimeType(),
                $this->formatFileSize($storedFile->getSize()),
                $storedFile->getCreatedAt()->format('d.m.Y H:i'),
                $storedFile->getOwner()
            );
        }

        /* @var TableHelper $table */
        $table = $this->getHelperSet()->get('table');
        $table
            ->setHeaders(array('#', 'Filename', 'Mime type', 'Size', 'Created', 'Owner'))
            ->setRows($rows)
        ;
        $table->render($output);
    }
} 