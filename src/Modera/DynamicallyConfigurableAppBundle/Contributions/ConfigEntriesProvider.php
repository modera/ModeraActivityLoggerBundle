<?php

namespace Modera\DynamicallyConfigurableAppBundle\Contributions;

use Modera\ConfigBundle\Config\ConfigurationEntryDefinition as CED;
use Modera\FoundationBundle\Translation\T;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Modera\DynamicallyConfigurableAppBundle\ModeraDynamicallyConfigurableAppBundle as Bundle;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ConfigEntriesProvider implements ContributorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        // "client" configuration configs are not that much important when standard foundation is used because
        // "general" category relies on "Modera.backend.dcmjr.view.GeneralSettingsPanel" to display
        // and edit configuration properties which defines all required configuration right in JS file

        $yes = T::trans('yes');
        $no = T::trans('no');

        $kernelDebugServer = array(
            'handler' => 'modera_config.boolean_handler',
            'update_handler' => 'modera_dynamically_configurable_app.value_handling.kernel_config_writer',
            'true_text' => $yes,
            'false_text' => $no,
        );
        $kernelDebugClient = array(
            'xtype' => 'combo',
            'store' => [['prod', 'yes'], ['dev', 'no']],
        );

        $kernelEnvServer = array(
            'handler' => 'modera_config.dictionary_handler',
            'update_handler' => 'modera_dynamically_configurable_app.value_handling.kernel_config_writer',
            'dictionary' => array(
                'prod' => $yes,
                'dev' => $no,
            ),
        );
        $kernelEnvClient = array(
            'xtype' => 'combo',
            'store' => [[true, 'yes'], [false, 'no']],
        );

        return array(
            new CED(Bundle::CONFIG_KERNEL_ENV, T::trans('Production mode'), 'prod', 'general', $kernelEnvServer, $kernelEnvClient),
            new CED(Bundle::CONFIG_KERNEL_DEBUG, T::trans('Maintenance mode'), false, 'general', $kernelDebugServer, $kernelDebugClient),
        );
    }
}
