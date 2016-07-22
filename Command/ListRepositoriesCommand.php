<?php

namespace Modera\FileRepositoryBundle\Command;

use Doctrine\ORM\EntityManager;
use Modera\FileRepositoryBundle\Entity\Repository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ListRepositoriesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('modera:file-repository:list')
            ->setDescription('Shows all available repositories')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $rows = array();
        foreach ($em->getRepository(Repository::clazz())->findAll() as $repository) {
            /* @var Repository $repository */

            $config = $repository->getConfig();

            $rows[] = array(
                $repository->getId(),
                $repository->getName(),
                $repository->getLabel() ? $repository->getLabel() : '-',
                $config['filesystem'],
                isset($config['overwrite_files']) ? $config['overwrite_files'] : false,
                $config['storage_key_generator'],
            );
        }

        /* @var TableHelper $table */
        $table = $this->getHelperSet()->get('table');
        $table
            ->setHeaders(array('#', 'Name', 'Label', 'Filesystem', 'Overwrite files', 'Storage key generator'))
            ->setRows($rows)
        ;
        $table->render($output);
    }
}
