<?php

namespace Modera\AdminGeneratorBundle\Contributions;

use Modera\JSRuntimeIntegrationBundle\Sections\Section;
use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class SectionsProvider implements ContributorInterface
{
    private $generatorConfigsProvider;

    /**
     * @param ContributorInterface $generatorConfigsProvider
     */
    public function __construct(ContributorInterface $generatorConfigsProvider)
    {
        $this->generatorConfigsProvider = $generatorConfigsProvider;
    }

    /**
     * @inheritDoc
     */
    public function getItems()
    {
        $result = array();

        foreach ($this->generatorConfigsProvider->getItems() as $generatorConfig) {
            if (isset($generatorConfig['runtime_section'])) {
                $sectionConfig = $generatorConfig['runtime_section'];

                if (is_array($sectionConfig)) {
                    $meta = array_merge($sectionConfig['meta'], array(
                        'generator_config' => $generatorConfig
                    ));

                    $result[] = new Section($sectionConfig['id'], $sectionConfig['controller'], $meta);
                } else if ($sectionConfig instanceof Section) {
                    $result[] = $sectionConfig;
                }
            }
        }

        return $result;
    }
}