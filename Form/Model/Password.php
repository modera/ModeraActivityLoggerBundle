<?php

namespace Modera\SecurityBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class Password
{
    /**
     * Plain password. Used for model validation. Must not be persisted.
     * @Assert\NotBlank()
     */
    protected $plainPassword;

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param string $password
     */
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }
}