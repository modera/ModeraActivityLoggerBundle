<?php

namespace Modera\RoutingBundle\Routing;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * Collects dynamically contributed routing resources.
 *
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
    private $resourcesProvider;

    /**
     * @var FileLocatorInterface
     */
    private $locator;

    /**
     * @var bool
     */
    private $isLoaded = false;

    /**
     * @param ContainerInterface $container
     * @param ContributorInterface $resourcesProvider
     * @param FileLocatorInterface $locator
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
    private function getRootRoutingLoader()
    {
        // we cannot use this in a class constructor because it will result in a circular dependency exception
        return $this->container->get('routing.loader');
    }

    /**
     * @inheritDoc
     */
    public function load($resource, $type = null)
    {
        if (true === $this->isLoaded) {
            throw new \RuntimeException('Do not add the "modera_routing" loader twice');
        }

        $resources = array();
        $items = $this->resourcesProvider->getItems();
        foreach ($items as $index => $resource) {
            if (!is_array($resource)) {
                $resource = array(
                    'order'    => 0,
                    'resource' => $resource,
                );
            }
            $resource['index'] = $index;
            $resources[] = $resource;
        }
        usort($resources, function($a, $b) {
            if ($a['order'] == $b['order']) {
                return ($a['index'] < $b['index']) ? -1 : 1;
            }
            return ($a['order'] < $b['order']) ? -1 : 1;
        });

        $collection = new RouteCollection();
        foreach ($resources as $item) {
            $resource = $this->locator->locate($item['resource']);
            $collection->addCollection($this->getRootRoutingLoader()->load($resource));
        }

        $this->isLoaded = true;

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
     * @inheritDoc
     */
    public function getResolver()
    {
    }

    /**
     * @inheritDoc
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
    }

    /**
     * @return bool
     */
    public function isLoaded()
    {
        return $this->isLoaded;
    }
}
