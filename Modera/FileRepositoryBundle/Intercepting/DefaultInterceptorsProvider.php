<?php

namespace Modera\FileRepositoryBundle\Intercepting;

use Modera\FileRepositoryBundle\Entity\Repository;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * You should not use this class directly.
 *
 * This implementation of interceptors provider looks at config available (Repository::getConfig())
 * in repository and if it has a configuration key "interceptors" then all strings inside this array
 * will be treated as service container ids and corresponding services will be fetched from dependency
 * injection container which should be implementations of {@link OperationInterceptorInterface}.
 *
 * @private
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2015 Modera Foundation
 */
class DefaultInterceptorsProvider implements InterceptorsProviderInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getInterceptors(Repository $repository)
    {
        $interceptors = array();

        $ids = array(
            'modera_file_repository.validation.file_properties_validation_interceptor',
        );

        $config = $repository->getConfig();
        if (isset($config['interceptors']) && is_array($config['interceptors'])) {
            $ids = array_merge($ids, $config['interceptors']);
        }

        foreach ($ids as $id) {
            $interceptors[] = $this->container->get($id);
        }

        return $interceptors;
    }
}
