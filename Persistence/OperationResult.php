<?php

namespace Modera\ServerCrudBundle\Persistence;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class OperationResult
{
    const TYPE_ENTITY_CREATED = 'entity_created';
    const TYPE_ENTITY_UPDATED = 'entity_updated';
    const TYPE_ENTITY_REMOVED = 'entity_removed';

    private $entries = array();

    /**
     * @param string $entityClass
     * @param string $id
     * @param string $operation
     */
    public function reportEntity($entityClass, $id, $operation)
    {
        $this->entries[] = array(
            'entity_class' => $entityClass,
            'operation' => $operation,
            'id' => $id
        );
    }

    private function findEntriesByOperation($operationName)
    {
        $result = array();

        foreach ($this->entries as $entry) {
            if ($entry['operation'] == $operationName) {
                $result[] = array(
                    'entity_class' => $entry['entity_class'],
                    'id' => $entry['id']
                );
            }
        }

        return $result;
    }

    /**
     * @return array[]
     */
    public function getCreatedEntities()
    {
        return $this->findEntriesByOperation(self::TYPE_ENTITY_CREATED);
    }

    /**
     * @return array[]
     */
    public function getUpdatedEntities()
    {
        return $this->findEntriesByOperation(self::TYPE_ENTITY_UPDATED);
    }

    /**
     * @return array[]
     */
    public function getRemovedEntities()
    {
        return $this->findEntriesByOperation(self::TYPE_ENTITY_REMOVED);
    }

    /**
     * @param ModelManagerInterface $modelMgr
     *
     * @return array
     */
    public function toArray(ModelManagerInterface $modelMgr)
    {
        $result = array();

        $mapping = array(
            'entity_created' => 'created_models',
            'entity_updated' => 'updated_models',
            'entity_removed' => 'removed_models'
        );

        foreach ($this->entries as $entry) {
            $key = $mapping[$entry['operation']];

            if (!isset($result[$key])) {
                $result[$key] = array();
            }

            $modelName = $modelMgr->generateModelIdFromEntityClass($entry['entity_class']);

            if (!isset($result[$key][$modelName])) {
                $result[$key][$modelName] = array();
            }

            $result[$key][$modelName][] = $entry['id'];
        }

        return $result;
    }

    /**
     * @param OperationResult $result
     *
     * @return OperationResult  A new instance of OperationResult is returned
     */
    public function merge(OperationResult $result)
    {
        $new = new self();
        foreach (array_merge($this->getCreatedEntities(), $result->getCreatedEntities()) as $entry) {
            $new->reportEntity($entry['entity_class'], $entry['id'], self::TYPE_ENTITY_CREATED);
        }
        foreach (array_merge($this->getUpdatedEntities(), $result->getUpdatedEntities()) as $entry) {
            $new->reportEntity($entry['entity_class'], $entry['id'], self::TYPE_ENTITY_UPDATED);
        }
        foreach (array_merge($this->getRemovedEntities(), $result->getRemovedEntities()) as $entry) {
            $new->reportEntity($entry['entity_class'], $entry['id'], self::TYPE_ENTITY_REMOVED);
        }

        return $new;
    }

    static public function clazz()
    {
        return get_called_class();
    }
}