<?php

namespace Modera\BackendDashboardBundle\Controller;

use Modera\BackendDashboardBundle\Dashboard\DashboardInterface;
use Modera\BackendDashboardBundle\Entity\SettingsEntityInterface;
use Modera\BackendDashboardBundle\Entity\UserSettings;
use Modera\ServerCrudBundle\Controller\AbstractCrudController;
use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author    Alex Rudakov <alexandr.rudakov@modera.org>
 * @copyright 2014 Modera Foundation
 */
class UserSettingsController extends AbstractSettingsController
{
    protected function getEntityClass()
    {
        return UserSettings::clazz();
    }
}