<?php

namespace Modera\FileRepositoryBundle\Command;

use Gaufrette\Adapter\Local;
use Gaufrette\File;
use Gaufrette\Filesystem;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class TestCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->
            setName('modera:file-repository:test')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('hoy');

        $adapter = new Local('/home/sergei/projects/modera-foundation/app/Resources');
        $fs = new Filesystem($adapter);

        $file = $fs->get('blah/foo.txt', true);
        $file->setContent('datavaj');

//        $fm->put($repository, $file);

        $file = new \Symfony\Component\HttpFoundation\File\File('xxx');


    }
} 