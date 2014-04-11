<?php

namespace Modera\BackendLanguagesBundle\Controller;

use Modera\BackendLanguagesBundle\Entity\UserSettings;
use Modera\ServerCrudBundle\Controller\AbstractCrudController;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class UserSettingsController extends AbstractCrudController
{
    /**
     * @return array
     */
    public function getConfig()
    {
        return array(
            'entity' => UserSettings::clazz(),
            'hydration' => array(
                'groups' => array(
                    'main-form' => function(UserSettings $settings) {
                        return array(
                            'id'       => $settings->getId(),
                            'language' => $settings->getLanguage() ? $settings->getLanguage()->getId() : null,
                        );
                    },
                ),
                'profiles' => array(
                    'main-form',
                )
            ),
        );
    }
}
