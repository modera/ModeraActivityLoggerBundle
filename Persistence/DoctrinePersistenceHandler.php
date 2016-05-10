<?php

namespace Modera\ServerCrudBundle\Persistence;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Sli\ExtJsIntegrationBundle\QueryBuilder\ExtjsQueryBuilder;

/**
 * Implementations of PersistenceHandlerInterface which eventually will use Doctrine's EntityManager to communicate
 * with database.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class DoctrinePersistenceHandler implements PersistenceHandlerInterface
{
    private $em;
    private $queryBuilder;

    /**
     * @param EntityManager     $em
     * @param ExtjsQueryBuilder $queryBuilder
     */
    public function __construct(EntityManager $em, ExtjsQueryBuilder $queryBuilder)
    {
        $this->em = $em;
        $this->queryBuilder = $queryBuilder;
    }

    private function resolveEntityId($entity)
    {
        // TODO improve
        return $entity->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function resolveEntityPrimaryKeyFields($entityClass)
    {
        $result = array();

        /* @var ClassMetadataInfo $meta */
        $meta = $this->em->getClassMetadata($entityClass);

        foreach ($meta->getFieldNames() as $fieldName) {
            $fieldMapping = $meta->getFieldMapping($fieldName);

            if (isset($fieldMapping['id']) && $fieldMapping['id']) {
                $result[] = $fieldName;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function save($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();

        $result = new OperationResult();
        $result->reportEntity(
            get_class($entity), $this->resolveEntityId($entity), OperationResult::TYPE_ENTITY_CREATED
        );

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function update($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();

        $result = new OperationResult();
        $result->reportEntity(
            get_class($entity), $this->resolveEntityId($entity), OperationResult::TYPE_ENTITY_UPDATED
        );

        return $result;
    }

    /**
     * @param object[] $entities
     *
     * @return OperationResult
     */
    public function updateBatch(array $entities)
    {
        $result = new OperationResult();

        foreach ($entities as $entity) {
            $this->em->persist($entity);

            $result->reportEntity(
                get_class($entity), $this->resolveEntityId($entity), OperationResult::TYPE_ENTITY_UPDATED
            );
        }

        $this->em->flush();

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function query($entityClass, array $query)
    {
        return $this->queryBuilder->buildQuery($entityClass, $query)->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getCount($entityClass, array $query)
    {
        $qb = $this->queryBuilder->buildQueryBuilder($entityClass, $query);

        return $this->queryBuilder->buildCountQueryBuilder($qb)->getQuery()->getSingleScalarResult();
    }

    /**
     * {@inheritdoc}
     */
    public function remove(array $entities)
    {
        $result = new OperationResult();

        foreach ($entities as $entity) {
            $this->em->remove($entity);

            $result->reportEntity(
                get_class($entity), $this->resolveEntityId($entity), OperationResult::TYPE_ENTITY_REMOVED
            );
        }

        $this->em->flush();

        return $result;
    }

    public static function clazz()
    {
        return get_called_class();
    }
}
