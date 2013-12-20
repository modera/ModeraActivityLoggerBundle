<?php

namespace Modera\AdminGeneratorBundle\Validation;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ValidatorInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class EntityValidator
{
    private $validator;
    private $container;

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator, ContainerInterface $container)
    {
        $this->validator = $validator;
        $this->container = $container;
    }

    /**
     * @param object $entity
     * @param array  $config
     *
     * @return ValidationResult
     */
    public function validate($entity, array $config)
    {
        $validationResult = new ValidationResult();

        if (false === $config['ignore_standard_validator']) {
            foreach ($this->validator->validate($entity) as $violation) {
                /* @var ConstraintViolationInterface $violation */

                $validationResult->addFieldError($violation->getPropertyPath(), $violation->getMessageTemplate());
            }
        }

        if (false !== $config['entity_validation_method'] && in_array($config['entity_validation_method'], get_class_methods($entity))) {
            $methodName = $config['entity_validation_method'];

            $entity->$methodName($validationResult, $this->container);
        }

        return $validationResult;
    }

    static public function clazz()
    {
        return get_called_class();
    }
}