<?php

namespace Modera\BackendTranslationsToolBundle\Contributions;

use Sli\ExpanderBundle\Ext\ContributorInterface;
use Modera\BackendTranslationsToolBundle\Filtering\Filter;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class FiltersProvider implements ContributorInterface
{
    /**
     * @var array
     */
    private $items = array();

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->items = array(
            'translation_token' => array(
                new Filter\AllTranslationTokensFilter($container),
                new Filter\NewTranslationTokensFilter($container),
                new Filter\ObsoleteTranslationTokensFilter($container),
            ),
        );
    }

    /**
     * @inheritDoc
     */
    public function getItems()
    {
        return $this->items;
    }
}
