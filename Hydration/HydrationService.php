<?php

namespace Modera\ServerCrudBundle\Hydration;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Service is responsible for converting given entity/entities to something that can be sent back to client-side.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class HydrationService
{
    private $container;
    private $accessor;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * @param mixed  $hydrator
     * @param object $object
     *
     * @return array
     */
    private function invokeHydrator($hydrator, $object)
    {
        if (is_callable($hydrator)) {
            return $hydrator($object, $this->container);
        } elseif (is_array($hydrator)) {
            $result = array();

            foreach ($hydrator as $key => $propertyPath) {
                $key = is_numeric($key) ? $propertyPath : $key;

                try {
                    $result[$key] = $this->accessor->getValue($object, $propertyPath);
                } catch (\Exception $e) {
                    throw new \RuntimeException(
                        "Unable to resolve expression '$propertyPath' on ".get_class($object), null, $e
                    );
                }
            }

            return $result;
        }

        return 'Invalid hydrator definition';
    }

    private function mergeHydrationResult(array $currentResult, array $hydratorResult, HydrationProfile $profile, $groupName)
    {
        if ($profile->isGroupingNeeded()) {
            $currentResult[$groupName] = $hydratorResult;
        } else {
            if (is_callable($hydratorResult)) {
                $currentResult = $hydratorResult($currentResult);
            } elseif (is_array($hydratorResult)) {
                $currentResult = array_merge($currentResult, $hydratorResult);
            } else {
                throw new \RuntimeException();
            }
        }

        return $currentResult;
    }

    /**
     * @param object          $object
     * @param array           $configAnalyzer
     * @param string          $profile
     * @param string|string[] $groups
     *
     * @return array
     */
    public function hydrate($object, array $config, $profile, $groups = null)
    {
        $configAnalyzer = new ConfigAnalyzer($config);

        /* @var HydrationProfile $profile */
        $profile = $configAnalyzer->getProfileDefinition($profile);

        if (null === $groups) { // going to hydrate all groups if none are explicitly specified
            $result = array();

            foreach ($profile->getGroups() as $groupName) {
                $hydrator = $configAnalyzer->getGroupDefinition($groupName);

                $hydratorResult = $this->invokeHydrator($hydrator, $object);

                $result = $this->mergeHydrationResult($result, $hydratorResult, $profile, $groupName);
            }

            return $result;
        } else {
            $groupsToUse = is_array($groups) ? array_values($groups) : array($groups);

            // if there's only one group given then no grouping is going to be used
            if (count($groupsToUse) == 1) {
                $hydrator = $configAnalyzer->getGroupDefinition($groupsToUse[0]);

                return $this->invokeHydrator($hydrator, $object);
            } else {
                $result = array();

                foreach ($groupsToUse as $groupName) {
                    $hydrator = $configAnalyzer->getGroupDefinition($groupName);

                    $hydratorResult = $this->invokeHydrator($hydrator, $object);

                    $result = $this->mergeHydrationResult($result, $hydratorResult, $profile, $groupName);
                }

                return $result;
            }
        }
    }
}
