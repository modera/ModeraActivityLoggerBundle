<?php

namespace Modera\BackendSecurityBundle\DataMapper;

use Modera\ServerCrudBundle\DataMapping\DefaultDataMapper;

/**
 * This class add support of excluded Fields, that will not me automatically
 * mapped.
 * Also User meta field mapping requires some by hand handling.
 *
 * @author    Alex Plaksin <alex.plaksin@modera.net>
 * @copyright 2016 Modera Foundation
 */
class UserDataMapper extends DefaultDataMapper
{
    /**
     * @var array
     */
    protected $excludedFields = array('meta');

    /**
     * {@inheritdoc}
     */
    protected function getAllowedFields($entityClass)
    {
        $me = $this;

        return array_filter(
            parent::getAllowedFields($entityClass),
            function ($fieldName) use ($me) {
                if (array_search($fieldName, $me->excludedFields) !== false) {
                    return false;
                }

                return true;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function mapData(array $params, $entity)
    {
        parent::mapData($params, $entity);

        if (array_key_exists('meta', $params)) {
            if (is_array($params['meta'])) {
                $entity->setMeta($params['meta']);
            } else {
                $entity->clearMeta();
            }
        }
    }
}
