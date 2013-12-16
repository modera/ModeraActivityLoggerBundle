<?php

namespace Modera\RoutingBundle\Routing;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouteCollection;

/**
 * @copyright 2013 Modera Foundation
 * @author Sergei Vizel <sergei.vizel@modera.org>
 */
class Loader implements LoaderInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var bool
     */
    private $loaded = false;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param mixed $resource
     * @param string $type
     * @return RouteCollection
     * @throws \RuntimeException
     */
    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "modera_routing" loader twice');
        }

        $collection = new RouteCollection();

        $kernel = $this->container->get('kernel');
        $loader = $this->container->get('routing.loader');
        foreach ($kernel->getBundles() as $bundle) {
            if (!($bundle instanceof RoutingInterface)) {
                continue;
            }
            $resource = $bundle->getRoutingResource();
            try {
                $resource = $this->container->get('file_locator')->locate($resource);
            } catch (\Exception $e) { // the conventionally located file doesn't exist
                continue;
            }

            $collection->addCollection($loader->load($resource));
        }

        $this->loaded = true;

        return $collection;
    }

    /**
     * @param mixed $resource
     * @param string $type
     * @return bool
     */
    public function supports($resource, $type = null)
    {
        return 'modera_routing' === $type;
    }

    /**
     * @return LoaderResolverInterface|void
     */
    public function getResolver()
    {
        //
    }

    /**
     * @param LoaderResolverInterface $resolver
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
        //
    }
}
