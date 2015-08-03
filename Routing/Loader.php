<?php

namespace Modera\RoutingBundle\Routing;

use Symfony\Bundle\FrameworkBundle\Routing\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Routing\Loader\YamlFileLoader;
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
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
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
     * @var bool
     */
    private $isLoaded = false;

    /**
     * @var LoaderInterface
     */
    private $rootLoader;

    /**
     * @param ContributorInterface $resourcesProvider
     * @param LoaderInterface $rootLoader
     */
    public function __construct(ContributorInterface $resourcesProvider, LoaderInterface $rootLoader)
    {
        $this->rootLoader = $rootLoader;
        $this->resourcesProvider = $resourcesProvider;
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
            $collection->addCollection($this->rootLoader->load($item['resource']));
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
