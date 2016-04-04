<?php

namespace Modera\RoutingBundle\Routing;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
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
     * @param LoaderInterface      $rootLoader
     */
    public function __construct(ContributorInterface $resourcesProvider, LoaderInterface $rootLoader)
    {
        $this->rootLoader = $rootLoader;
        $this->resourcesProvider = $resourcesProvider;
    }

    /**
     * {@inheritdoc}
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
                    'resource' => $resource,
                );
            }
            $resource = array_merge(array('order' => 0, 'type' => null), $resource);
            $resource['index'] = $index;
            $resources[] = $resource;
        }

        usort($resources, function ($a, $b) {
            if ($a['order'] == $b['order']) {
                return ($a['index'] < $b['index']) ? -1 : 1;
            }

            return ($a['order'] < $b['order']) ? -1 : 1;
        });

        $collection = new RouteCollection();
        foreach ($resources as $item) {
            $collection->addCollection($this->rootLoader->load($item['resource'], $item['type']));
        }

        $this->isLoaded = true;

        return $collection;
    }

    /**
     * @param mixed  $resource
     * @param string $type
     *
     * @return bool
     */
    public function supports($resource, $type = null)
    {
        return 'modera_routing' === $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getResolver()
    {
    }

    /**
     * {@inheritdoc}
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
