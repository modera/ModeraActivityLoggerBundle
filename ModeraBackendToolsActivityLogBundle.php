<?php

namespace Modera\BackendToolsActivityLogBundle;

use Sli\ExpanderBundle\Contributing\ExtensionPointsAwareBundleInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Modera\MjrIntegrationBundle\Sections\Section as MJRSection;
use Modera\BackendToolsBundle\Section\Section as ToolsSection;
use Modera\FoundationBundle\Translation\T;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ModeraBackendToolsActivityLogBundle extends Bundle implements ExtensionPointsAwareBundleInterface
{
    /**
     * @inheritDoc
     */
    public function getExtensionPointContributions()
    {
        return array(
            'modera_mjr_integration.css_resources_provider' => array(
                '/bundles/moderabackendtoolsactivitylog/css/styles.css'
            ),
            'modera_backend_tools.sections_provider' => array(
                new ToolsSection(
                    T::trans('Activity log'),
                    'tools.activitylog',
                    T::trans('See what activities recently have happened on the site'),
                    '', '',
                    'modera-backend-tools-activity-log-icon'
                )
            ),
            'modera_mjr_integration.sections_provider' => array(
                new MJRSection('tools.activitylog', 'Modera.backend.tools.activitylog.runtime.Section', array(
                    MJRSection::META_NAMESPACE => 'Modera.backend.tools.activitylog',
                    MJRSection::META_NAMESPACE_PATH => '/bundles/moderabackendtoolsactivitylog/js'
                ))
            )
        );
    }

}
