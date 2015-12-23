<?php

namespace Modera\BackendSecurityBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Modera\SecurityBundle\Entity\User;
use Modera\FoundationBundle\Translation\T;
use Modera\BackendLanguagesBundle\Entity\UserSettings;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class MailService implements MailServiceInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @var string
     */
    private $mailSender;

    /**
     * @param \Swift_Mailer $mailer
     */
    public function __construct(EntityManagerInterface $em, \Swift_Mailer $mailer, $defaultLocale = 'en', $mailSender = 'no-reply@no-reply')
    {
        $this->em = $em;
        $this->mailer = $mailer;
        $this->defaultLocale = $defaultLocale;
        $this->mailSender = $mailSender;
    }

    /**
     * @param User $user
     * @param $plainPassword
     *
     * @return array|bool
     */
    public function sendPassword(User $user, $plainPassword)
    {
        /* @var \Swift_Message $message */
        $message = $this->mailer->createMessage();

        $locale = $this->getLocale($user);
        $subject = T::trans('Your password', array(), 'mail', $locale);
        $body = T::trans('Your new password is: %plainPassword%', array('%plainPassword%' => $plainPassword), 'mail', $locale);

        $message->setFrom($this->mailSender);
        $message->setTo($user->getEmail());
        $message->setSubject($subject);
        $message->setBody($body);

        $failedRecipients = array();
        if (!$this->mailer->send($message, $failedRecipients)) {
            return $failedRecipients;
        }

        return true;
    }

    /**
     * @param User $user
     *
     * @return string
     */
    private function getLocale(User $user)
    {
        $settings = $this->em->getRepository(UserSettings::clazz())->findOneBy(array('user' => $user->getId()));
        if ($settings && $settings->getLanguage() && $settings->getLanguage()->getEnabled()) {
            return $settings->getLanguage()->getLocale();
        }

        return $this->defaultLocale;
    }
}
