<?php

namespace Modera\BackendTranslationsToolBundle\Filtering;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
interface FilterInterface
{
    /**
     * Technical name of filter. Used as a key in arrays/forms.
     *
     * @return string
     */
    public function getId();

    /**
     * Human readable name of filter.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns filtered data.
     *
     * Example:
     * array(
     *     'success' => boolean,
     *     'items'   => Object[],
     *     'total'   => int
     * )
     *
     * @param array $params
     *
     * @return array
     */
    public function getResult(array $params);

    /**
     * Returns total.
     *
     * @param array $params
     *
     * @return int
     */
    public function getCount(array $params);

    /**
     * Checks if filter is allowed.
     *
     * @return bool
     */
    public function isAllowed();
}
