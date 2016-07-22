<?php

namespace Modera\BackendTranslationsToolBundle\Contributions;

use Sli\ExpanderBundle\Ext\ContributorInterface;
use Modera\MjrIntegrationBundle\Config\ConfigMergerInterface;
use Modera\BackendTranslationsToolBundle\Filtering\FilterInterface;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ConfigMergersProvider implements ContributorInterface, ConfigMergerInterface
{
    /**
     * @var FiltersProvider
     */
    private $filtersProvider;

    /**
     * @param FiltersProvider $filtersProvider
     */
    public function __construct(ContributorInterface $filtersProvider)
    {
        $this->filtersProvider = $filtersProvider;
    }

    /**
     * @param array $currentConfig
     *
     * @return array
     */
    public function merge(array $currentConfig)
    {
        $filters = array();
        foreach ($this->filtersProvider->getItems() as $key => $arr) {
            $filters[$key] = array();

            /* @var FilterInterface $iteratedFilter */
            foreach ($arr as $iteratedFilter) {
                if (!$iteratedFilter->isAllowed()) {
                    continue;
                }

                $filters[$key][] = array(
                    'id' => $iteratedFilter->getId(),
                    'name' => $iteratedFilter->getName(),
                );
            }
        }

        return array_merge($currentConfig, array(
            'modera_backend_translations_tool' => array(
                'filters' => $filters,
            ),
        ));
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getItems()
    {
        return array($this);
    }
}
