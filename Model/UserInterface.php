<?php

namespace Modera\SecurityBundle\Model;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
interface UserInterface
{
    const GENDER_MALE = 'm';
    const GENDER_FEMALE = 'f';

    const STATE_NEW = 0;
    const STATE_ACTIVE = 1;

    public function getEmail();
    public function getFirstName();
    public function getLastName();
    public function getMiddleName();
    public function getFullName();
    public function getGender();
    public function getState();
}