<?php

namespace Modera\FileRepositoryBundle\Command;

use Doctrine\ORM\EntityManager;
use Modera\FileRepositoryBundle\Repository\FileRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class DeleteRepositoryCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('modera:file-repository:delete-repository')
            ->setDescription('Deletes a repository with all its files')
            ->addArgument('repository', InputArgument::REQUIRED)
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var FileRepository $fr */
        $fr = $this->getContainer()->get('modera_file_repository.repository.file_repository');

        /* @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $repository = $fr->getRepository($input->getArgument('repository'));
        if (!$repository) {
            throw new \RuntimeException(sprintf(
                'Unable to find a repository with name "%s"', $input->getArgument('repository')
            ));
        }

        if (count($repository->getFiles()) > 0) {
            /* @var DialogHelper $dialog */
            $dialog = $this->getHelperSet()->get('dialog');

            $question = sprintf(
                'Repository "%s" contains %s files, are you sure that you want to delete this repository with all these files ? [Y/n]: ',
                $repository->getName(), count($repository->getFiles())
            );

            $answer = $dialog->askConfirmation($output, $question);
            if ($answer) {
                $output->writeln(sprintf('Deleting repository "%s"', $repository->getName()));

                $em->remove($repository);
                $em->flush();

                $output->writeln('Done!');
            } else {
                $output->writeln('Aborting ...');
            }
        } else {
            $output->writeln(sprintf('Deleting repository "%s"', $repository->getName()));

            $em->remove($repository);
            $em->flush();

            $output->writeln('Done!');
        }
    }
}
