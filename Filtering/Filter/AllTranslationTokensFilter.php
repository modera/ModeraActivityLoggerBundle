<?php

namespace Modera\BackendTranslationsToolBundle\Filtering\Filter;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class AllTranslationTokensFilter extends AbstractTranslationTokensFilter
{
    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return 'all';
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'All';
    }
} 