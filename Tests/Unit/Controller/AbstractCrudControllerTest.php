<?php

namespace Modera\ServerCrudBundle\Tests\Unit\Controller;

use Modera\ServerCrudBundle\DependencyInjection\ModeraServerCrudExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Modera\ServerCrudBundle\Controller\AbstractCrudController;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author    Alex Plaksin <alex.plaksin@modera.net>
 * @copyright 2016 Modera Foundation
 */
class AbstractCrudControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetDataMapper_ContainerParameter()
    {
        $config = array('data_mapper' => 'configDefinedMapper');

        /** @var ContainerBuilder $container */
        $container = \Phake::partialMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $container->setParameter(ModeraServerCrudExtension::CONFIG_KEY, $config);
        $container->compile();

        \Phake::when($container)->get('configDefinedMapper')->thenReturn(true);

        /** @var AbstractCrudController $controller */
        $controller = \Phake::partialMock('Modera\ServerCrudBundle\Controller\AbstractCrudController');
        $controller->setContainer($container);

        \Phake::when($controller)->getConfig()->thenReturn(
            array('entity' => 'testValue', 'hydration' => 'testValue')
        );

        $this->assertTrue(\Phake::makeVisible($controller)->getDataMapper());
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function testGetDataMapper_InConfigParameter_ServiceNotPresentInDIContainer()
    {
        $config = array('data_mapper' => 'configDefinedMapper');

        /** @var ContainerBuilder $container */
        $container = \Phake::partialMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $container->setParameter(ModeraServerCrudExtension::CONFIG_KEY, $config);
        $container->compile();

        \Phake::when($container)->get('configDefinedMapper')->thenReturn(true);

        /** @var AbstractCrudController $controller */
        $controller = \Phake::partialMock('Modera\ServerCrudBundle\Controller\AbstractCrudController');
        $controller->setContainer($container);

        \Phake::when($controller)->getConfig()->thenReturn(
            array('create_default_data_mapper' => function (ContainerInterface $container) {
                return $container->get('nonExistingService');
            }, 'entity' => 'testValue', 'hydration' => 'testValue')
        );

        \Phake::makeVisible($controller)->getDataMapper();
    }

    public function testGetDataMapper_InConfigParameter_AllOk()
    {
        $config = array('data_mapper' => 'configDefinedMapper');

        /** @var ContainerBuilder $container */
        $container = \Phake::partialMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $container->setParameter(ModeraServerCrudExtension::CONFIG_KEY, $config);
        $container->compile();

        \Phake::when($container)->get('configDefinedMapper')->thenReturn(false);
        \Phake::when($container)->has('existingService')->thenReturn(true);
        \Phake::when($container)->get('existingService')->thenReturn(true);

        /** @var AbstractCrudController $controller */
        $controller = \Phake::partialMock('Modera\ServerCrudBundle\Controller\AbstractCrudController');
        $controller->setContainer($container);

        \Phake::when($controller)->getConfig()->thenReturn(
            array(
                'create_default_data_mapper' => function (ContainerInterface $container) {
                    return $container->get('existingService');
                },
                'entity' => 'testValue', 'hydration' => 'testValue', )
        );

        $this->assertTrue(\Phake::makeVisible($controller)->getDataMapper());
    }

    /**
     * @expectedException \Modera\ServerCrudBundle\Exceptions\BadConfigException
     * @expectedExceptionMessage An error occurred while getting a configuration property "nonExisingService". No such property exists in config.
     */
    public function testGetConfiguredService_NoConfigOption()
    {
        $config = array('nonExistingService' => 'configDefinedMapper');

        /** @var ContainerBuilder $container */
        $container = \Phake::partialMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $container->setParameter(ModeraServerCrudExtension::CONFIG_KEY, $config);
        $container->compile();

        /** @var AbstractCrudController $controller */
        $controller = \Phake::partialMock('Modera\ServerCrudBundle\Controller\AbstractCrudController');
        $controller->setContainer($container);

        \Phake::makeVisible($controller)->getConfiguredService('nonExisingService');
    }

    /**
     * @expectedException \Modera\ServerCrudBundle\Exceptions\BadConfigException
     * @expectedExceptionMessage An error occurred while getting a service for configuration property "entity_validator" using DI service with ID "nonExistingServiceId" - You have requested a non-existent service "nonexistingserviceid".
     */
    public function testGetConfiguredService_NoContainerService()
    {
        $config = array('entity_validator' => 'nonExistingServiceId');

        /** @var ContainerBuilder $container */
        $container = \Phake::partialMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $container->setParameter(ModeraServerCrudExtension::CONFIG_KEY, $config);
        $container->compile();

        /** @var AbstractCrudController $controller */
        $controller = \Phake::partialMock('Modera\ServerCrudBundle\Controller\AbstractCrudController');
        $controller->setContainer($container);

        \Phake::makeVisible($controller)->getConfiguredService('entity_validator');
    }
}
