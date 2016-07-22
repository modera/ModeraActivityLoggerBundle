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
class DeleteFileCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('modera:file-repository:delete-file')
            ->setDescription('Deletes a file from repository')
            ->addArgument('file_id', InputArgument::REQUIRED)
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
        $storedFile = $em->find(StoredFile::clazz(), $input->getArgument('file_id'));
        if (!$storedFile) {
            throw new \RuntimeException('Unable to find a file with ID '.$input->getArgument('file_id'));
        }

        $output->writeln(sprintf(
            'Deleting file "%s" from repository "%s"',
            $storedFile->getFilename(), $storedFile->getRepository()->getName()
        ));

        $em->remove($storedFile);
        $em->flush();

        $output->writeln('<info>Done!</info>');
    }
}
