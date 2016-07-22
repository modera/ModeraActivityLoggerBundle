<?php

namespace Modera\BackendSecurityBundle\Tests\Unit\Service;

use Modera\SecurityBundle\Entity\User;
use Modera\BackendSecurityBundle\Service\MailService;
use Modera\BackendLanguagesBundle\Entity\UserSettings;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class MailServiceTest extends \PHPUnit_Framework_TestCase
{
    private $em;
    private $mailer;

    protected function setUp()
    {
        $this->em = \Phake::mock('Doctrine\ORM\EntityManagerInterface');
        \Phake::when($this->em)->getRepository(UserSettings::clazz())->thenReturn(new DummyRepository());

        $this->mailer = new DummySwiftMailer(\Phake::mock('Swift_Transport'));
    }

    public function testSuccessfulSendPassword()
    {
        $ms = new MailService($this->em, $this->mailer, 'en', 'no-reply@no-reply');
        $user = new User();
        $user->setEmail('successful.send.password@test.mail');
        $this->assertTrue($ms->sendPassword($user, 'password'));
    }

    public function testFailureSendPassword()
    {
        $ms = new MailService($this->em, $this->mailer, 'en', 'no-reply@no-reply');
        $user = new User();
        $user->setEmail('failure.send.password@test.mail');
        $this->assertTrue(is_array($ms->sendPassword($user, 'password')));
    }
}

class DummyRepository
{
    public function findOneBy()
    {
        return;
    }
}

class DummySwiftMailer extends \Swift_Mailer
{
    public function createMessage($service = 'message')
    {
        return \Swift_Message::newInstance();
    }

    public function send(\Swift_Mime_Message $message, &$failedRecipients = null)
    {
        if (in_array('failure.send.password@test.mail', array_keys($message->getTo()))) {
            return 0;
        }

        return 1;
    }
}
