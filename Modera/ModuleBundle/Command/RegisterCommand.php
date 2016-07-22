<?php

namespace Modera\ModuleBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Modera\ModuleBundle\Manipulator\KernelManipulator;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class RegisterCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('modera:module:register')
            ->setDescription('Updates AppKernel class so it would be able to dynamically load bundles.')
            ->setDefinition(array(
                new InputArgument(
                    'file',
                    InputArgument::REQUIRED,
                    'A name of a file which will hold bundles which will be dynamically instantiated.'
                ),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        /* @var KernelInterface $kernel */
        $kernel = $this->getContainer()->get('kernel');

        $km = new KernelManipulator($kernel);
        $resp = $km->addCode($file);

        if ($resp) {
            $output->writeln('');
            $output->writeln(implode('', array(
                '<info>',
                    'Method "registerModuleBundles" has been successfully added to app/AppKernel.php!',
                '</info>',
            )));
            $output->writeln('');
        }
    }
}
