<?php

namespace Modera\BackendSecurityBundle\Service;

use Modera\SecurityBundle\Entity\User;
use Modera\TranslationsBundle\Helper\T;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class MailService
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @param \Swift_Mailer $mailer
     */
    public function __construct(\Swift_Mailer $mailer, $defaultLocale = 'en')
    {
        $this->mailer = $mailer;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @param User $user
     * @param $plainPassword
     * @return array|bool
     */
    public function sendPassword(User $user, $plainPassword)
    {
        /* @var \Swift_Message $swiftMessage */
        $swiftMessage = $this->mailer->createMessage();

        $from    = T::trans('no-reply@no-reply', array(), 'mail', $this->defaultLocale);
        $to      = $user->getEmail();
        $subject = T::trans('Your password', array(), 'mail', $this->defaultLocale);
        $body    = T::trans('Your new password is: %plainPassword%', array('%plainPassword%' => $plainPassword), 'mail', $this->defaultLocale);
        $message = $swiftMessage->setFrom($from)->setTo($to)->setSubject($subject)->setBody($body);

        $failedRecipients = array();
        if (!$this->mailer->send($message, $failedRecipients)) {
            return $failedRecipients;
        }

        return true;
    }
}
