<?php

namespace Modera\BackendTranslationsToolBundle\Filtering\Filter;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class AllTranslationTokensFilter extends AbstractTranslationTokensFilter
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'all';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'All';
    }
}
