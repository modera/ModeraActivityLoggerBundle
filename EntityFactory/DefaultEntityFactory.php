<?php

namespace Modera\ServerCrudBundle\EntityFactory;

use Sli\AuxBundle\Util\Toolkit;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class DefaultEntityFactory implements EntityFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(array $params, array $config)
    {
        $entityClass = $config['entity'];

        // if __construct method doesn't have any mandatory parameters the we will use it
        $useConstructor = false;
        foreach (Toolkit::getReflectionMethods($entityClass) as $reflMethod) {
            /* @var \ReflectionMethod $reflMethod */
            if ($reflMethod->getName() == '__construct'
                && $reflMethod->isPublic()) {
                if (count($reflMethod->getParameters()) == 0) {
                    $useConstructor = true;
                } else { // if all parameters are optional we still can use constructor
                    $allParametersOptional = true;
                    foreach ($reflMethod->getParameters() as $reflParam) {
                        /* @var \ReflectionParameter $reflParam */
                        if (!$reflParam->isOptional()) {
                            $allParametersOptional = false;
                        }
                    }
                    $useConstructor = $allParametersOptional;
                }
            }
        }

        if ($useConstructor) {
            return new $entityClass();
        } else {
            $serialized = sprintf('O:%u:"%s":0:{}', strlen($entityClass), $entityClass);

            return unserialize($serialized);
        }
    }
}
