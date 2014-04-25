<?php

namespace Modera\DynamicallyConfigurableAppBundle\Contributions;

use Modera\ConfigBundle\Config\ConfigurationEntryDefinition as CED;
use Modera\TranslationsBundle\Helper\T;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Modera\DynamicallyConfigurableAppBundle\ModeraDynamicallyConfigurableAppBundle as Bundle;

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
        $yes = T::trans('yes');
        $no = T::trans('no');

        $kernelDebug = array(
            'id' => 'modera_config.boolean_handler',
            'true_text' => $yes,
            'false_text' => $no
        );

        $kernelEnv = array(
            'id' => 'modera_config.dictionary_handler',
            'dictionary' => array(
                'prod' => $yes,
                'dev' => $no
            )
        );

        return array(
            new CED(Bundle::CONFIG_KERNEL_ENV, 'Production mode', 'prod', $kernelEnv),
            new CED(Bundle::CONFIG_KERNEL_DEBUG, 'Maintenance mode', false, $kernelDebug)
        );
    }
}