<?php

namespace Modera\ServerCrudBundle\Validation;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
interface EntityValidatorInterface
{
    /**
     * @param object $entity
     * @param array  $config
     *
     * @return ValidationResult
     */
    public function validate($entity, array $config);
}
