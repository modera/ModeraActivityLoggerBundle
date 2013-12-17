<?php

namespace Modera\RoutingBundle\Routing;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2013 Modera Foundation
 */
class Loader implements LoaderInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ContributorInterface
     */
    protected $resourcesProvider;

    /**
     * @var FileLocatorInterface
     */
    private $locator;

    /**
     * @var bool
     */
    private $loaded = false;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container, ContributorInterface $resourcesProvider, FileLocatorInterface $locator)
    {
        $this->container = $container;
        $this->resourcesProvider = $resourcesProvider;
        $this->locator = $locator;
    }

    /**
     * @return LoaderInterface
     */
    private function getLoader()
    {
        return $this->container->get('routing.loader');
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
        $resources = $this->resourcesProvider->getItems();

        foreach ($resources as $resource) {
            $resource = $this->locator->locate($resource);
            $collection->addCollection($this->getLoader()->load($resource));
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
