<?php

namespace Modera\FoundationBundle\Util;

/**
 * @internal
 *
 * @copyright 2013 Modera Foundation
 * @author Sergei Lissovski <sergei.lissovski@modera.net>
 */ 
class ControllerServiceNameFormatter
{
    private $cache = array();

    /**
     * For a controller with FQCN MyCompany\Bundle\FooBundle\Controller\MyController or
     * MyCompany\FooBundle\Controller\MyController will return "mycompany.foo".
     *
     * @throws \RuntimeException
     * @param string $class
     * @return string
     */
    public function formatPrefix($class)
    {
        if (!isset($this->cache[$class])) {
            $controllerNamespace = explode('\\', $class);
            array_pop($controllerNamespace);

            $bundleNamespace = null;
            foreach ($controllerNamespace as $i=>$segment) {
                if ('Controller' == $segment) {
                    $bundleNamespace = array_slice($controllerNamespace, 0, $i);
                }
            }
            if (!$bundleNamespace) {
                throw new \RuntimeException(
                    'Unable to find "Controller" segment in provided class namespace - '.$class
                );
            }

            if (count($bundleNamespace) >= 3) {
                // if bundle is placed in "Bundle" directory, getting rid of it, for example
                // MyCompany/Bundle/MyBundle will be converted to this - MyCompany/MyBundle
                if ('Bundle' == $bundleNamespace[count($bundleNamespace)-2]) {
                    unset($bundleNamespace[count($bundleNamespace)-2]);
                    $bundleNamespace = array_values($bundleNamespace); // resetting numeric keys order
                }
            }

            // getting rid of "Bundle" suffix
            $bundleNamespace[count($bundleNamespace)-1] = preg_replace(
                '/(.*)Bundle$/', '$1', end($bundleNamespace)
            );

            $this->cache[$class] = strtolower(implode('.', $bundleNamespace));
        }
        return $this->cache[$class];
    }
}
