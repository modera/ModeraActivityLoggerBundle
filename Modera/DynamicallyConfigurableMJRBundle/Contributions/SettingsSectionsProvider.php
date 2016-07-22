<?php

namespace Modera\DynamicallyConfigurableMJRBundle\Contributions;

use Modera\BackendToolsSettingsBundle\Section\StandardSection;
use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class SettingsSectionsProvider implements ContributorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return [
            new StandardSection(
                'general',
                'General',
                'Modera.backend.dcmjr.runtime.GeneralSiteSettingsActivity',
                'gear',
                array(
                    'activationParams' => array(
                        'category' => 'general',
                    ),
                )
            ),
        ];
    }

    /**
     * @return string
     */
    public static function clazz()
    {
        return get_called_class();
    }
}
