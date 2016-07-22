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
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('modera:file-repository:create')
            ->setDescription('Command allows to create a file repository')
            ->addArgument('name', InputArgument::REQUIRED)
            ->addArgument('filesystem', InputArgument::REQUIRED)
            ->addArgument('label', InputArgument::OPTIONAL)
            ->addOption('overwrite-files', 'o', InputOption::VALUE_NONE, 'Overwrite files with same name')
            ->addOption(
                'preserve-extensions',
                null,
                InputOption::VALUE_NONE,
                'If specified then stored files will not lose their original extensions (.png, .txt etc)'
            )
            ->addOption(
                'images-only',
                null,
                InputOption::VALUE_NONE,
                'If specified then it will be possible to upload only images to the created repository. '
            )
            ->addOption(
                'max-size',
                null,
                InputOption::VALUE_OPTIONAL,
                'Allows to set a file size limit, sample values: 100k, 5m'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var FileRepository $fr */
        $fr = $this->getContainer()->get('modera_file_repository.repository.file_repository');

        $config = array(
            'filesystem' => $input->getArgument('filesystem'),
            'overwrite_files' => $input->getOption('overwrite-files'),
        );

        if ($input->getOption('preserve-extensions')) {
            $output->writeln(' <comment>Important security note</comment>');
            $output->writeln(' By using <info>--preserve-extensions</info> a created repository will keep original extensions of');
            $output->writeln(' files that are stored in it and if you are using a local filesystem (which keeps files on the same');
            $output->writeln(' server from where they are uploaded) then this might be a source of very significat security');
            $output->writeln(' threat - imagine, if uploaded files are stored in a publicly accessible from web directory ');
            $output->writeln(" (like 'web') then if a user uploaded a PHP file then he will be able to execute it by going");
            $output->writeln(' http://youdomain.com/uploadedfile.php and your server will execute it. In order to avoid this ');
            $output->writeln(' security threat you have several options:');
            $output->writeln(' - controlling MIME types/file extensions when files are uploaded, and use some white list ');
            $output->writeln('   to allow files only of certain type to be uploaded - like .php, .jpg (take a look at ');
            $output->writeln('   <info>--images-only</info> flag)');
            $output->writeln(" - configure your web-server in a way that it won't execute any scripts from a publicly accessible");
            $output->writeln('   directories where users can upload files.');
            $output->write(PHP_EOL);

            $config['storage_key_generator'] = 'modera_file_repository.repository.uniqid_key_generator_preserved_extension';
        }

        if ($input->getOption('images-only')) {
            $config['images_only'] = true;
        }
        if ($input->getOption('max-size')) {
            $config['max_size'] = $input->getOption('max-size');
        }

        $repository = $fr->createRepository($input->getArgument('name'), $config, $input->getArgument('label'));

        $output->writeln(
            ' <info>Success!</info> Repository has been successfully created! Its internal is #'.$repository->getId()
        );
    }
}
