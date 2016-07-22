<?php

namespace Modera\BackendToolsSettingsBundle\Contributions;

use Modera\BackendToolsSettingsBundle\Section\SectionInterface;
use Modera\MjrIntegrationBundle\Config\ConfigMergerInterface;
use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * Merges settings sections to MJR runtime-config.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class SectionsConfigMerger implements ConfigMergerInterface
{
    private $sectionsProvider;

    /**
     * @param ContributorInterface $sectionsProvider  Settings sections provider
     */
    public function __construct(ContributorInterface $sectionsProvider)
    {
        $this->sectionsProvider = $sectionsProvider;
    }

    /**
     * @inheritDoc
     */
    public function merge(array $existingConfig)
    {
        $existingConfig['settingsSections'] = array();

        foreach ($this->sectionsProvider->getItems() as $section) {
            /* @var SectionInterface $section */

            $existingConfig['settingsSections'][] = array(
                'id' => $section->getId(),
                'name' => $section->getName(),
                'activityClass' => $section->getActivityClass(),
                'glyph' => $section->getGlyph(),
                'meta' =>$section->getMeta()
            );
        }

        return $existingConfig;
    }
}