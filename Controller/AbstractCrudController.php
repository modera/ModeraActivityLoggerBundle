<?php

namespace Modera\ServerCrudBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Modera\ServerCrudBundle\DataMapping\DataMapperInterface;
use Modera\ServerCrudBundle\DependencyInjection\ModeraServerCrudExtension;
use Modera\ServerCrudBundle\EntityFactory\EntityFactoryInterface;
use Modera\ServerCrudBundle\ExceptionHandling\ExceptionHandlerInterface;
use Modera\ServerCrudBundle\Exceptions\BadRequestException;
use Modera\ServerCrudBundle\Exceptions\MoreThanOneResultException;
use Modera\ServerCrudBundle\Exceptions\NothingFoundException;
use Modera\ServerCrudBundle\Hydration\HydrationService;
use Modera\ServerCrudBundle\Intercepting\ControllerActionsInterceptorInterface;
use Modera\ServerCrudBundle\Intercepting\InterceptorsManager;
use Modera\ServerCrudBundle\NewValuesFactory\NewValuesFactoryInterface;
use Modera\ServerCrudBundle\Persistence\ModelManagerInterface;
use Modera\ServerCrudBundle\Persistence\OperationResult;
use Modera\ServerCrudBundle\Persistence\PersistenceHandlerInterface;
use Modera\ServerCrudBundle\Validation\DefaultEntityValidator;
use Modera\ServerCrudBundle\Validation\ValidationResult;
use Modera\FoundationBundle\Controller\AbstractBaseController;
use Modera\DirectBundle\Annotation\Remote;
use Sli\AuxBundle\Util\Toolkit;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class provides tools for fulfilling CRUD operations.
 *
 * When you create a subclass of AbstractCrudController you must implement `getConfig` method which must
 * contain at least two configuration properties:
 *
 * - entity -- Fully qualified class name of entity this controller will be responsible for
 * - hydration -- Data hydration rules
 *
 * For more details on other available configuration properties and general how-tos please refer to the bundle's
 * README.md file.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
abstract class AbstractCrudController extends AbstractBaseController implements CrudControllerInterface
{
    /**
     * @return array
     */
    abstract public function getConfig();

    /**
     * @return array
     */
    public function getPreparedConfig()
    {
        $defaultConfig = array(
            'create_entity' => function(array $params, array $config, EntityFactoryInterface $defaultFactory, ContainerInterface $container) {
                return $defaultFactory->create($params, $config);
            },
            'map_data_on_create' => function(array $params, $entity, DataMapperInterface $defaultMapper, ContainerInterface $container) {
                $defaultMapper->mapData($params, $entity);
            },
            'map_data_on_update' => function(array $params, $entity, DataMapperInterface $defaultMapper, ContainerInterface $container) {
                $defaultMapper->mapData($params, $entity);
            },
            'new_entity_validator' => function(array $params, $mappedEntity, DefaultEntityValidator $defaultValidator, array $config, ContainerInterface $container) {
                return $defaultValidator->validate($mappedEntity, $config);
            },
            'updated_entity_validator' => function(array $params, $mappedEntity, DefaultEntityValidator $defaultValidator, array $config, ContainerInterface $container) {
                return $defaultValidator->validate($mappedEntity, $config);
            },
            'save_entity_handler' => function($entity, array $params, PersistenceHandlerInterface $defaultHandler, ContainerInterface $container) {
                return $defaultHandler->save($entity);
            },
            'update_entity_handler' => function($entity, array $params, PersistenceHandlerInterface $defaultHandler, ContainerInterface $container) {
                return $defaultHandler->update($entity);
            },
            'batch_update_entities_handler' => function(array $entities, array $params, PersistenceHandlerInterface $defaultHandler, ContainerInterface $container) {
                return $defaultHandler->updateBatch($entities);
            },
            'remove_entities_handler' => function(array $entities, array $params, PersistenceHandlerInterface $defaultHandler, ContainerInterface $container) {
                return $defaultHandler->remove($entities);
            },
            'exception_handler' => function(\Exception $e, $operation, ExceptionHandlerInterface $defaultHandler, ContainerInterface $container) {
                return $defaultHandler->createResponse($e, $operation);
            },
            'format_new_entity_values' => function(array $params, array $config, NewValuesFactoryInterface $defaultImpl, ContainerInterface $container) {
                return $defaultImpl->getValues($params, $config);
            },
            // optional
            'ignore_standard_validator' => false,
            // optional
            'entity_validation_method' => 'validate'
        );

        $config = array_merge($defaultConfig, $this->getConfig());

        if (!isset($config['entity'])) {
            throw new \RuntimeException("'entity' configuration property is not defined.");
        }
        if (!isset($config['hydration'])) {
            throw new \RuntimeException("'hydration' configuration property is not defined.");
        }

        return $config;
    }

    /**
     * @param array $params
     *
     * @return object
     */
    private function createEntity(array $params)
    {
        $config = $this->getPreparedConfig();

        $defaultFactory = $this->getEntityFactory();

        $entity = call_user_func_array($config['create_entity'], array($params, $config, $defaultFactory, $this->container));
        if (!$entity) {
            throw new \RuntimeException("Configured factory didn't create an object.");
        }

        return $entity;
    }

    /**
     * @param string $serviceType
     * @return object
     */
    private function getConfiguredService($serviceType)
    {
        $config = $this->container->getParameter(ModeraServerCrudExtension::CONFIG_KEY);

        return $this->container->get($config[$serviceType]);
    }

    /**
     * @return PersistenceHandlerInterface
     */
    protected function getPersistenceHandler()
    {
        return $this->getConfiguredService('persistence_handler');
    }

    /**
     * @return ModelManagerInterface
     */
    private function getModelManager()
    {
        return $this->getConfiguredService('model_manager');
    }

    /**
     * @return DefaultEntityValidator
     */
    private function getEntityValidator()
    {
        return $this->getConfiguredService('entity_validator');
    }

    /**
     * @return \Modera\ServerCrudBundle\DataMapping\DataMapperInterface
     */
    private function getDataMapper()
    {
        return $this->getConfiguredService('data_mapper');
    }

    /**
     * @return EntityFactoryInterface
     */
    private function getEntityFactory()
    {
        return $this->getConfiguredService('entity_factory');
    }

    /**
     * @return ExceptionHandlerInterface
     */
    private function getExceptionHandler()
    {
        return $this->getConfiguredService('exception_handler');
    }

    /**
     * @return HydrationService
     */
    private function getHydrator()
    {
        return $this->getConfiguredService('hydrator');
    }

    /**
     * @return NewValuesFactoryInterface
     */
    private function getNewValuesFactory()
    {
        return $this->getConfiguredService('new_values_factory');
    }

    /**
     * @param object $entity
     * @param array  $params
     * @param string $defaultProfile
     *
     * @return array
     */
    final protected function hydrate($entity, array $params)
    {
        if (!isset($params['hydration']['profile'])) {
            $e = new BadRequestException('Hydration profile is not specified.');
            $e->setPath('/hydration/profile');
            $e->setParams($params);

            throw $e;
        }

        $profile = $params['hydration']['profile'];
        $groups = isset($params['hydration']['group']) ? $params['hydration']['group'] : null;

        $config = $this->getPreparedConfig();
        $hydrationConfig = $config['hydration'];

        return $this->getHydrator()->hydrate($entity, $hydrationConfig, $profile, $groups);
    }

    final protected function createExceptionResponse(\Exception $e, $operation)
    {
        $config = $this->getPreparedConfig();

        $exceptionHandler = $config['exception_handler'];

        return $exceptionHandler($e, $operation, $this->getExceptionHandler(), $this->container);
    }

    /**
     * @Remote
     */
    public function createAction(array $params)
    {
        try {
            $this->interceptAction('create', $params);

            if (!isset($params['record'])) {
                $e = new BadRequestException("'/record' hasn't been provided");
                $e->setParams($params);
                $e->setPath('/record');

                throw $e;
            }

            $entity = $this->createEntity($params);

            return $this->saveOrUpdateEntityAndCreateResponse($params, $entity, 'create');
        } catch (\Exception $e) {
            return $this->createExceptionResponse($e, ExceptionHandlerInterface::OPERATION_CREATE);
        }
    }

    /**
     * @param array $params
     * @param object $entity
     * @param string $operationType
     *
     * @return array
     */
    private function saveOrUpdateEntityAndCreateResponse(array $params, $entity, $operationType)
    {
        $config = $this->getPreparedConfig();

        $dataMapper = $config['map_data_on_' . $operationType];
        $persistenceHandler = $config[('create' == $operationType ? 'save' : 'update') . '_entity_handler'];
        $validator = $config[('create' == $operationType ? 'new' : 'updated') . '_entity_validator'];

        if ($dataMapper) {
            $dataMapper($params['record'], $entity, $this->getDataMapper(), $this->container);
        }

        if ($validator) {
            /* @var ValidationResult $validationResult */
            $validationResult = $validator($params, $entity, $this->getEntityValidator(), $config, $this->container);
            if ($validationResult->hasErrors()) {
                return array_merge($validationResult->toArray(), array(
                    'success' => false
                ));
            }
        }

        /* @var OperationResult $operationResult */
        $operationResult = $persistenceHandler($entity, $params, $this->getPersistenceHandler(), $this->container);

        $response = array(
            'success' => true
        );

        $response = array_merge($response, $operationResult->toArray($this->getModelManager()));

        if (isset($params['hydration'])) {
            $response = array_merge(
                $response, array('result' => $this->hydrate($entity, $params))
            );
        }

        return $response;
    }

    /**
     * Validates that result form query has exactly one value.
     *
     * @param array $entities
     * @param array $params
     *
     * @throws \Modera\ServerCrudBundle\Exceptions\NothingFoundException
     * @throws \Modera\ServerCrudBundle\Exceptions\MoreThanOneResultException
     */
    private function validateResultHasExactlyOneEntity(array $entities, array $params)
    {
        if (count($entities) > 1) {
            throw new MoreThanOneResultException(sprintf(
                'Query must return exactly one result, but %d were returned', count($entities)
            ));
        }

        if (count($entities) == 0) {
            throw new NothingFoundException('Query must return exactly one result, but nothing was returned');
        }
    }

    private function interceptAction($actionName, $params)
    {
        /* @var InterceptorsManager $mgr */
        $mgr = $this->get('modera_server_crud.intercepting.interceptors_manager');

        $mgr->intercept($actionName, $params, $this);
    }

    /**
     * @Remote
     */
    public function getAction(array $params)
    {
        $config = $this->getPreparedConfig();

        try {
            $this->interceptAction('get', $params);

            $entities = $this->getPersistenceHandler()->query($config['entity'], $params);

            $this->validateResultHasExactlyOneEntity($entities, $params);

            $hydratedEntity = $this->hydrate($entities[0], $params);

            return array(
                'success' => true,
                'result' => $hydratedEntity
            );
        } catch (\Exception $e) {
            return $this->createExceptionResponse($e, ExceptionHandlerInterface::OPERATION_GET);
        }
    }

    /**
     * @Remote
     *
     * @param array $params
     */
    public function listAction(array $params)
    {
        $config = $this->getPreparedConfig();

        try {
            $this->interceptAction('list', $params);

            $total = $this->getPersistenceHandler()->getCount($config['entity'], $params);

            $hydratedItems = array();
            foreach ($this->getPersistenceHandler()->query($config['entity'], $params) as $entity) {
                $hydratedItems[] = $this->hydrate($entity, $params);
            }

            return array(
                'success' => true,
                'items' => $hydratedItems,
                'total' => $total
            );
        } catch (\Exception $e) {
            return $this->createExceptionResponse($e, ExceptionHandlerInterface::OPERATION_LIST);
        }
    }

    /**
     * @Remote
     *
     * @param array $params
     */
    public function removeAction(array $params)
    {
        $config = $this->getPreparedConfig();

        try {
            $this->interceptAction('remove', $params);

            if (!isset($params['filter'])) {
                $e = new BadRequestException("'/filter' parameter hasn't been provided");
                $e->setParams($params);
                $e->setPath('/filter');

                throw $e;
            }

            $persistenceHandler = $config['remove_entities_handler'];

            $entities = $this->getPersistenceHandler()->query($config['entity'], $params);

            /* @var OperationResult $operationResult */
            $operationResult = $persistenceHandler($entities, $params, $this->getPersistenceHandler(), $this->container);

            return array_merge(
                array('success' => true),
                $operationResult->toArray($this->getModelManager())
            );
        } catch (\Exception $e) {
            return $this->createExceptionResponse($e, ExceptionHandlerInterface::OPERATION_REMOVE);
        }
    }

    /**
     * @Remote
     */
    public function getNewRecordValuesAction(array $params)
    {
        $config = $this->getPreparedConfig();

        try {
            $this->interceptAction('getNewRecordValues', $params);

            $newValuesFactory = $config['format_new_entity_values'];

            return $newValuesFactory($params, $config, $this->getNewValuesFactory(), $this->container);
        } catch (\Exception $e) {
            return $this->createExceptionResponse($e, ExceptionHandlerInterface::OPERATION_GET_NEW_RECORD_VALUES);
        }
    }

    /**
     * @Remote
     */
    public function updateAction(array $params)
    {
        $config = $this->getPreparedConfig();

        try {
            $this->interceptAction('update', $params);

            if (!isset($params['record'])) {
                $e = new BadRequestException("'/record' hasn't been provided");
                $e->setParams($params);
                $e->setPath('/record');

                throw $e;
            }

            $recordParams = $params['record'];

            $missingPkFields = array();
            $query = array();
            foreach ($this->getPersistenceHandler()->resolveEntityPrimaryKeyFields($config['entity']) as $fieldName) {
                if (isset($recordParams[$fieldName])) {
                    $query[] = array(
                        'property' => $fieldName,
                        'value' => 'eq:' . $recordParams[$fieldName]
                    );
                } else {
                    $missingPkFields[] = $fieldName;
                }
            }
            if (count($missingPkFields)) {
                $e = new BadRequestException('These primary key fields were not provided: ' . implode(', ', $missingPkFields));
                $e->setParams($params);
                $e->setPath('/');

                throw $e;
            }

            $entities = $this->getPersistenceHandler()->query($config['entity'], array('filter' => $query));

            $this->validateResultHasExactlyOneEntity($entities, $params);

            return $this->saveOrUpdateEntityAndCreateResponse($params, $entities[0], 'update');
        } catch (\Exception $e) {
            return $this->createExceptionResponse($e, ExceptionHandlerInterface::OPERATION_UPDATE);
        }
    }

    /**
     * @Remote
     */
    public function batchUpdateAction(array $params)
    {
        $config = $this->getPreparedConfig();

        try {
            $persistenceHandler = $config['batch_update_entities_handler'];
            $dataMapper = $config['map_data_on_update'];
            $validator = $config['updated_entity_validator'];

            $this->interceptAction('batchUpdate', $params);

            if (   isset($params['queries']) && is_array($params['queries'])
                && isset($params['record']) && is_array($params['record'])) {

                if (!isset($params['record'])) {
                    $e = new BadRequestException("'/record' hasn't been provided");
                    $e->setParams($params);
                    $e->setPath('/record');

                    throw $e;
                }

                $entities = array();
                foreach ($params['queries'] as $query) {
                    $entities = array_merge($entities, $this->getPersistenceHandler()->query($config['entity'], $query));
                }

                $errors = array();
                $operationResult = null;
                foreach ($entities as $entity) {
                    $dataMapper($params['record'], $entity, $this->getDataMapper(), $this->container);

                    if ($validator) {
                        /* @var ValidationResult $validationResult */
                        $validationResult = $validator($params, $entity, $this->getEntityValidator(), $config, $this->container);
                        if ($validationResult->hasErrors()) {
                            $pkFields = $this->getPersistenceHandler()->resolveEntityPrimaryKeyFields($config['entity']);

                            $ids = array();
                            foreach($pkFields as $fieldName) {
                                $ids[$fieldName] = Toolkit::getPropertyValue($entity, $fieldName);
                            }

                            $errors[] = array(
                                'id' => $ids,
                                'errors' => $validationResult->toArray()
                            );
                        }
                    }
                }

                if (count($errors) == 0) {
                    $operationResult = $persistenceHandler($entities, $params, $this->getPersistenceHandler(), $this->container);

                    return array_merge($operationResult->toArray($this->getModelManager()), array(
                        'success' => true
                    ));
                } else {
                    return array(
                        'success' => false,
                        'errors' => $errors
                    );
                }
            } else if (isset($params['records']) && is_array($params['records'])) {
                $entities = array();
                $errors = array();
                foreach ($params['records'] as $recordParams) {
                    $missingPkFields = array();
                    $query = array();
                    foreach ($this->getPersistenceHandler()->resolveEntityPrimaryKeyFields($config['entity']) as $fieldName) {
                        if (isset($recordParams[$fieldName])) {
                            $query[] = array(
                                'property' => $fieldName,
                                'value' => 'eq:' . $recordParams[$fieldName]
                            );
                        } else {
                            $missingPkFields[] = $fieldName;
                        }
                    }

                    if (count($missingPkFields) == 0) {
                        $entity = $this->getPersistenceHandler()->query($config['entity'], array('filter' => $query));
                        $this->validateResultHasExactlyOneEntity($entity, $params);
                        $entity = $entity[0];

                        $entities[] = $entity;

                        $dataMapper($recordParams, $entity, $this->getDataMapper(), $this->container);

                        if ($validator) {
                            /* @var ValidationResult $validationResult */
                            $validationResult = $validator($params, $entity, $this->getEntityValidator(), $config, $this->container);
                            if ($validationResult->hasErrors()) {
                                $pkFields = $this->getPersistenceHandler()->resolveEntityPrimaryKeyFields($config['entity']);

                                $ids = array();
                                foreach($pkFields as $fieldName) {
                                    $ids[$fieldName] = Toolkit::getPropertyValue($entity, $fieldName);
                                }

                                $errors[] = array(
                                    'id' => $ids,
                                    'errors' => $validationResult->toArray()
                                );
                            }
                        }
                    }
                }

                if (count($errors) == 0) {
                    $operationResult = $persistenceHandler($entities, $params, $this->getPersistenceHandler(), $this->container);

                    return array_merge($operationResult->toArray($this->getModelManager()), array(
                        'success' => true
                    ));
                } else {
                    return array(
                        'success' => false,
                        'errors' => $errors
                    );
                }
            } else {
                $e = new BadRequestException(
                    "Invalid request structure. Valid request would either contain 'queries' and 'record' or 'records' keys."
                );
                $e->setPath($params);
                $e->setPath('/');

                throw $e;
            }
        } catch (\Exception $e) {
            return $this->createExceptionResponse($e, ExceptionHandlerInterface::OPERATION_UPDATE);
        }
    }

    static public function clazz()
    {
        return get_called_class();
    }
}