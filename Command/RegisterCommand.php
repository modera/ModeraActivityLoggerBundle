<?php

namespace Modera\ModuleBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Modera\ModuleBundle\Manipulator\KernelManipulator;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class RegisterCommand extends ContainerAwareCommand
{
    // override
    protected function configure()
    {
        $this
            ->setName('modera:module:register')
            ->setDescription('Register bundles.')
            ->setDefinition(array(
                new InputArgument('file', InputArgument::REQUIRED, 'The file of the bundles'),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
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
