<?php

namespace Modera\SecurityBundle\Model;

/**
 * Allows to categorize implementations of {@class PermissionInterface}s.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
interface PermissionCategoryInterface
{
    /**
     * A unique ID that can later be used by {@class PermissionInterface} to refer a category, for example:
     * "customer_support".
     *
     * @return string
     */
    public function getTechnicalName();

    /**
     * A human readable name of a category, for example - "Customer support".
     *
     * @return string
     */
    public function getName();
}
