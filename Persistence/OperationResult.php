<?php

namespace Modera\AdminGeneratorBundle\Persistence;

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

    /**
     * @param ModelManagerInterface $modelMgr
     *
     * @return array
     */
    public function toArray(ModelManagerInterface $modelMgr)
    {
        $result = array(
            'created_models' => array(),
            'updated_models' => array(),
            'removed_models' => array()
        );

        $mapping = array(
            'entity_created' => 'created_models',
            'entity_updated' => 'updated_models',
            'entity_removed' => 'removed_models'
        );

        foreach ($this->entries as $entry) {
            $result[$mapping[$entry['operation']]][] = $modelMgr->generateModelIdFromEntityClass($entry['entity_class']);
        }

        return $result;
    }
}