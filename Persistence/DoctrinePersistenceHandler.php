<?php

namespace Modera\AdminGeneratorBundle\Persistence;

use Doctrine\ORM\EntityManager;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class DoctrinePersistenceHandler implements PersistenceHandlerInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
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
     * @param array $query
     *
     * @return object[]
     */
    public function query(array $query)
    {

    }

    static public function clazz()
    {
        return get_called_class();
    }
}