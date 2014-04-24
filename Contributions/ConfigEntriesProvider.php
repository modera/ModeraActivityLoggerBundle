<?php

namespace Modera\DynamicallyConfigurableMJRBundle\Contributions;

use Modera\ConfigBundle\Config\ConfigurationEntryDefinition as CED;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Modera\DynamicallyConfigurableMJRBundle\ModeraDynamicallyConfigurableMJRBundle as Bundle;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ConfigEntriesProvider implements ContributorInterface
{
    /**
     * @inheritDoc
     */
    public function getItems()
    {
        return array(
            new CED(Bundle::CONFIG_TITLE, 'Site name', 'Modera Foundation'),
            new CED(Bundle::CONFIG_URL, 'Default URL', ''),
            new CED(Bundle::CONFIG_HOME_SECTION, 'Defautl section to open when user logs in to backend', 'dashboard')
        );
    }
}