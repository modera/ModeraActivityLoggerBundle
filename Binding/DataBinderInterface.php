<?php

namespace Modera\AdminGeneratorBundle\Binding;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
interface DataBinderInterface
{
    public function bind(array $params, $entity);
}