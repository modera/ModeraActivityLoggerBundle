<?php

namespace Modera\BackendTranslationsToolBundle\Controller;

use Modera\LanguagesBundle\Entity\Language;
use Modera\ServerCrudBundle\Controller\AbstractCrudController;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class LanguagesController extends AbstractCrudController
{
    /**
     * @return array
     */
    public function getConfig()
    {
        return array(
            'entity' => Language::clazz(),
            'hydration' => array(
                'groups' => array(
                    'list' => ['id', 'name', 'locale', 'isEnabled'],
                ),
                'profiles' => array(
                    'list',
                )
            ),
        );
    }
}
