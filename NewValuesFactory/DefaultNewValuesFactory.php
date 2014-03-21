<?php

namespace Modera\ServerCrudBundle\NewValuesFactory;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Will try to find a static method 'formatNewValues' on an entity resolved by using $config parameter
 * passed to getValues() method, if the method is found the following values will be passed:
 * $params, $config and instance of ContainerInterface. The static method must return a serializable data
 * structure that eventually will be sent bank to client-side.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class DefaultNewValuesFactory implements NewValuesFactoryInterface
{
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function getValues(array $params, array $config)
    {
        $entityClass = $config['entity'];

        $methodName = 'formatNewValues';

        if (method_exists($entityClass, $methodName)) {
            $reflClass = new \ReflectionClass($entityClass);
            $reflMethod = $reflClass->getMethod($methodName);
            if ($reflMethod->isStatic()) {
                return $reflMethod->invokeArgs(null, array($params, $config, $this->container));
            }
        }

        return array();
    }
} 