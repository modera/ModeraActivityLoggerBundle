<?php

namespace Modera\SecurityBundle\Model;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
interface UserInterface
{
    public function getEmail();
    public function getFirstName();
    public function getLastName();
    public function getMiddleName();
    public function getFullName();
    public function getGender();
}