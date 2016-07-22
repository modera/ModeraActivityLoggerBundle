<?php

namespace Modera\FileRepositoryBundle\Command;

use Doctrine\ORM\EntityManager;
use Modera\FileRepositoryBundle\Entity\StoredFile;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class DownloadFileCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('modera:file-repository:download-file')
            ->setDescription('Downloads a file to local filesystem')
            ->addArgument('file_id', InputArgument::REQUIRED)
            ->addArgument('local_path', InputArgument::REQUIRED)
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        /* @var StoredFile $storedFile */
        $storedFile = $em->getRepository(StoredFile::clazz())->find($input->getArgument('file_id'));
        if (!$storedFile) {
            throw new \RuntimeException(sprintf('Unable to find a file with ID "%s".', $input->getArgument('file_id')));
        }

        $localPath = $input->getArgument('local_path');

        $output->writeln('Downloading the file ...');

        ob_start();
        $result = file_put_contents($localPath, $storedFile->getContents());
        $errorOutput = ob_get_clean();

        if (false !== $result) {
            $output->writeln(sprintf(
                '<info>File from repository "%s" has been successfully downloaded and stored locally at %s</info>',
                $storedFile->getRepository()->getName(), $localPath
            ));
        } else {
            $output->writeln('<error>Something went wrong, we were unable to save a file locally: </error>');
            $output->writeln($errorOutput);
        }
    }
}
