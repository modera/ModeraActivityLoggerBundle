<?php

namespace Modera\AdminGeneratorBundle\Controller;

use Modera\AdminGeneratorBundle\Binding\DataBinderInterface;
use Modera\AdminGeneratorBundle\Binding\DataBindingInterface;
use Modera\AdminGeneratorBundle\EntityFactory\EntityFactoryInterface;
use Modera\AdminGeneratorBundle\ExceptionHandling\ExceptionHandlerInterface;
use Modera\AdminGeneratorBundle\Persistence\ModelManagerInterface;
use Modera\AdminGeneratorBundle\Persistence\OperationResult;
use Modera\AdminGeneratorBundle\Persistence\PersistenceHandlerInterface;
use Modera\AdminGeneratorBundle\Validation\EntityValidator;
use Modera\AdminGeneratorBundle\Validation\ValidationResult;
use Modera\FoundationBundle\Controller\AbstractBaseController;
use Neton\DirectBundle\Annotation\Remote;
use Symfony\Bridge\Doctrine\DependencyInjection\Security\UserProvider\EntityFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class DataController extends AbstractBaseController
{
    public function getConfig()
    {
        return array(
            'entity' => ''
        );
    }

    /**
     * @return array
     */
    public function getPreparedConfig()
    {
        $defaultConfig = array(
            'create_entity' => function(array $params, array $config, EntityFactoryInterface $defaultFactory, ContainerInterface $container) {
                return $defaultFactory->create($params, $config);
            },
            'bind_data_on_create' => function(array $params, $entity, DataBinderInterface $defaultBinder, ContainerInterface $container) {
                return $defaultBinder->bind($params, $entity);
            },
            'bind_data_on_update' => function(array $params, $entity, DataBinderInterface $defaultBinder, ContainerInterface $container) {
                return $defaultBinder->bind($params, $entity);
            },
            'new_record_validator' => function(array $params, $mappedEntity, EntityValidator $defaultValidator, array $config, ContainerInterface $container) {
                return $defaultValidator->validate($mappedEntity, $config);
            },
            'updated_record_validator' => function(array $params, $mappedEntity, EntityValidator $defaultValidator, array $config, ContainerInterface $container) {
                return $defaultValidator->validate($mappedEntity, $config);
            },
            'save_entity_handler' => function($entity, array $params, PersistenceHandlerInterface $defaultHandler, ContainerInterface $container) {
                return $defaultHandler->save($entity);
            },
            'update_entity_handler' => function($entity, array $params, PersistenceHandlerInterface $defaultHandler, ContainerInterface $container) {
                return $defaultHandler->update($entity);
            },
            'exception_handler' => function(\Exception $e, $operation, ExceptionHandlerInterface $defaultHandler, ContainerInterface $container) {
                return $defaultHandler->createResponse($e, $operation);
            },
            // optional
            'ignore_standard_validator' => false,
            // optional
            'entity_validation_method' => 'validate',
            'hydration' => array(
                'default_gateway' => 'list',
                'data_gateways' => array(
                    'list' => array(
                        'security_role' => 'ROLE_FOO',
                        'profiles' => array(
                            'main' => function($entity) {
                                return array(
                                    'id' => $entity->getId()
                                );
                            }
                        )
                    )
                )
            )
        );

        $request = array(
            '_gateway' => 'list'
        );

        $config = array_merge($defaultConfig, $this->getConfig());

        if (!isset($config['entity'])) {
            throw new \RuntimeException("'entity' configuration property is not defined.");
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
     * @return PersistenceHandlerInterface
     */
    private function getPersistenceHandler()
    {
        $config = $this->getPreparedConfig();

        return $config['persistence_handler'];
    }

    /**
     * @return ModelManagerInterface
     */
    private function getModelManager()
    {
        $config = $this->getPreparedConfig();

        return $config['model_manager'];
    }

    /**
     * @return EntityValidator
     */
    private function getEntityValidator()
    {
        return $this->container->get('');
    }

    /**
     * @return DataBinderInterface
     */
    private function getDataBinder()
    {

    }

    /**
     * @return EntityFactoryInterface
     */
    private function getEntityFactory()
    {
        return $this->container->get('');
    }

    /**
     * @return ExceptionHandlerInterface
     */
    private function getExceptionHandler()
    {
        return $this->container->get('');
    }

    /**
     * @Remote
     */
    public function createAction(array $params)
    {
        $config = $this->getPreparedConfig();

        try {
            $entity = $this->createEntity($params);

            $dataBinder = $config['bind_data_on_create'];
            if ($dataBinder) {
                $dataBinder($params, $entity, $this->getDataBinder(), $this->container);
            }

            $validator = $config['new_record_validator'];
            if ($validator) {
                /* @var ValidationResult $validationResult */
                $validationResult = $validator($params, $entity, $this->getEntityValidator(), $config, $this->container);
                if ($validationResult->hasErrors()) {
                    return array($validationResult->toArray(), array(
                        'success' => false
                    ));
                }
            }

            $saveHandler = $config['save_entity_handler'];

            /* @var OperationResult $operationResult */
            $operationResult = $saveHandler($entity, $params, $this->getPersistenceHandler(), $this->container);

            $response = array(
                'success' => true
            );
            $response = array_merge($response, $operationResult->toArray($this->getModelManager()));

            return $response;
        } catch (\Exception $e) {
            $exceptionHandler = $config['exception_handler'];

            return $exceptionHandler($e, 'create', $this->container);
        }
    }

    /**
     * @Remote
     *
     * @param array $params
     */
    public function listAction(array $params)
    {
        return array(
            $this->getRecord()
        );
    }

    /**
     * @Remote
     *
     * @param array $params
     */
    public function removeAction(array $params)
    {
        return array(
            'records_removed' => array('customer'),
            'success' => true
        );
    }

    /**
     * @Remote
     */
    public function getNewRecordValuesAction(array $params)
    {
        return array(
            'firstname' => '?'
        );
    }

    /**
     * @Remote
     */
    public function updateAction(array $params)
    {
        return array(
            'success' => true,
            'updated_models' => array(
                'modera.ecommerce.customer'
            )
        );
    }

    /**
     * @Remote
     */
    public function getAction(array $params)
    {
        return array(
            'success' => true,
            'record' => $this->getRecord()
        );
    }
}