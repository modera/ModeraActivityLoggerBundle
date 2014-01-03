<?php

namespace Modera\ServerCrudBundle\Hydration;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * This hydrator relies on existence of service container with id "doctrine.orm.entity_manager".
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class DoctrineEntityHydrator
{
    private $type;

    private $accessor;

    private $excludedFields = array();
    private $associativeFieldMappings = array();

    /**
     * @return DoctrineEntityHydrator
     */
    static public function create(array $excludedFields = array())
    {
        $me = new self();
        $me->excludeFields($excludedFields);

        return $me;
    }

    /**
     * @throws \LogicException
     *
     * @param string[] $fields
     *
     * @return DoctrineEntityHydrator
     */
    public function excludeFields(array $fields)
    {
        $this->excludedFields = $fields;

        return $this;
    }

    /**
     * @param string $relationFieldName
     * @param string $expression
     *
     * @return DoctrineEntityHydrator
     */
    public function mapRelation($relationFieldName, $expression)
    {
        $this->associativeFieldMappings[$relationFieldName] = $expression;

        return $this;
    }

    /**
     * @param object             $entity
     * @param ContainerInterface $container
     *
     * @return array
     */
    public function __invoke($entity, ContainerInterface $container)
    {
        /* @var EntityManager $em */
        $em = $container->get('doctrine.orm.entity_manager');

        if (!$this->accessor) {
            $this->accessor = PropertyAccess::createPropertyAccessor();
        }

        $meta = $em->getClassMetadata(get_class($entity));

        $result = array();
        foreach ($meta->getFieldNames() as $fieldName) {
            $result[$fieldName] = $this->accessor->getValue($entity, $fieldName);
        }

        $hasToStringMethod = in_array('__toString', get_class_methods(get_class($entity)));

        foreach ($meta->getAssociationNames() as $fieldName) {
            if (isset($this->associativeFieldMappings[$fieldName])) {
                $expression = $this->associativeFieldMappings[$fieldName];

                $result[$fieldName] = $this->accessor->getValue($entity, $expression);
            } else if ($hasToStringMethod) {
                $result[$fieldName] = $entity->__toString();
            }
        }

        $finalResult = array();

        foreach ($result as $fieldName => $fieldValue) {
            if (in_array($fieldName, $this->excludedFields)) {
                continue;
            }

            $finalResult[$fieldName] = $fieldValue;
        }

        return $finalResult;
    }
}