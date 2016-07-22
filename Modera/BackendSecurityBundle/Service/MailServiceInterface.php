<?php

namespace Modera\BackendSecurityBundle\Service;

use Modera\SecurityBundle\Entity\User;

/**
 * @author    Stas Chychkan <stas.chichkan@modera.net>
 * @copyright 2015 Modera Foundation
 */
interface MailServiceInterface
{
    /**
     * @param User $user
     * @param $plainPassword
     *
     * @return array|bool
     */
    public function sendPassword(User $user, $plainPassword);
}
