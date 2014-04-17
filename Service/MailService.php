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
     * @param \Swift_Mailer $mailer
     */
    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param User $user
     * @param $plainPassword
     * @return array|bool
     */
    public function sendPassword(User $user, $plainPassword)
    {
        $from    = T::trans('no-reply@no-reply');
        $to      = $user->getEmail();
        $subject = T::trans('Your password');
        $body    = T::trans('Your new password is: %plainPassword%', array('%plainPassword%' => $plainPassword));
        $message = \Swift_Message::newInstance()->setFrom($from)->setTo($to)->setSubject($subject)->setBody($body);

        $failedRecipients = array();
        if (!$this->mailer->send($message, $failedRecipients)) {
            return $failedRecipients;
        }

        return true;
    }
}
