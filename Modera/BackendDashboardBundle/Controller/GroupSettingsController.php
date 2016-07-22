<?php

namespace Modera\BackendDashboardBundle\Controller;

use Modera\BackendCarStockBundle\Entity\UserSettings;
use Modera\BackendDashboardBundle\Entity\GroupSettings;
use Modera\SecurityBundle\Entity\Group;
use Modera\ServerCrudBundle\Controller\AbstractCrudController;

/**
 * @author    Alex Rudakov <alexandr.rudakov@modera.org>
 * @copyright 2014 Modera Foundation
 */
class GroupSettingsController extends AbstractSettingsController
{
    protected function getEntityClass()
    {
        return GroupSettings::clazz();
    }

}