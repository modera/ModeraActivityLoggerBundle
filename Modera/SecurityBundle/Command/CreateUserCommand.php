<?php

namespace Modera\SecurityBundle\Command;

use Doctrine\ORM\EntityManager;
use Modera\SecurityBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class CreateUserCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('modera:security:create-user')
            ->setDescription('Allows to create a user that you can later user to authenticate.')
            ->addOption('no-interactions', null, InputOption::VALUE_NONE)
            ->addOption('username', null, InputOption::VALUE_OPTIONAL)
            ->addOption('email', null, InputOption::VALUE_OPTIONAL)
            ->addOption('password', null, InputOption::VALUE_OPTIONAL)
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $username = $input->getOption('username');
        $email = $input->getOption('email');
        $password = $input->getOption('password');

        if (false === $input->getOption('no-interactions')) {
            /* @var DialogHelper $dialog */
            $dialog = $this->getHelper('dialog');

            $output->writeln('<info>This command will let you to create a test user that you can user to authenticated to administration interface</info>');
            $output->write(PHP_EOL);

            $username = $dialog->ask($output, '<question>Username:</question> ');
            $email = $dialog->ask($output, '<question>Email:</question> ');

            do {
                $password = $dialog->askHiddenResponse($output, '<question>Password:</question> ');
                $passwordConfirm = $dialog->askHiddenResponse($output, '<question>Password again:</question> ');

                if ($password != $passwordConfirm) {
                    $output->writeln('<error>Entered passwords do not match, please try again</error>');
                }
            } while ($password != $passwordConfirm);
        }

        /* @var UserPasswordEncoderInterface $encoder */
        $encoder = $this->getContainer()->get('security.password_encoder');

        $user = new User();
        $user->setEmail($email);
        $user->setUsername($username);
        $user->setPassword($encoder->encodePassword($user, $password));

        $em->persist($user);
        $em->flush();

        $output->writeln('');
        $output->writeln(sprintf(
            '<info>Great success! User "%s" has been successfully created!</info>',
            $user->getUsername()
        ));
    }
}
