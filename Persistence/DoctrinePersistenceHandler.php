<?php

namespace Modera\AdminGeneratorBundle\Persistence;

use Doctrine\ORM\EntityManager;
use Sli\ExtJsIntegrationBundle\QueryBuilder\ExtjsQueryBuilder;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class DoctrinePersistenceHandler implements PersistenceHandlerInterface
{
    private $em;
    private $queryBuilder;

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
     * @param object $entity
     *
     * @return OperationResult
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
     * @param object $entity
     *
     * @return OperationResult
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
     * @inheritDoc
     */
    public function query($entityClass, array $query)
    {
        return $this->queryBuilder->buildQuery($entityClass, $query)->getResult();
    }

    /**
     * @inheritDoc
     */
    public function getCount($entityClass, array $query)
    {
        $qb = $this->queryBuilder->buildQueryBuilder($entityClass, $query);

        return $this->queryBuilder->buildCountQueryBuilder($qb)->getQuery()->getSingleScalarResult();
    }

    /**
     * @inheritDoc
     */
    public function remove($entityClass, array $params)
    {
        $result = new OperationResult();

        foreach ($this->queryBuilder->buildQuery($entityClass, $params)->getResult() as $entity) {
            $this->em->remove($entity);

            $result->reportEntity($entityClass, $this->resolveEntityId($entity), OperationResult::TYPE_ENTITY_REMOVED);
        }

        $this->em->flush();

        return $result;
    }

    static public function clazz()
    {
        return get_called_class();
    }
}