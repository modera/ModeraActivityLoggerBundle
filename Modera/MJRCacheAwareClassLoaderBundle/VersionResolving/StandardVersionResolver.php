<?php

namespace Modera\MJRCacheAwareClassLoaderBundle\VersionResolving;

use Modera\MJRCacheAwareClassLoaderBundle\DependencyInjection\ModeraMJRCacheAwareClassLoaderExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Standard version resolver will try to do the following things in order to resolve currently installed MF version:.
 *
 *  * at first it will try use bundle semantic config's configuration property "version"
 *   ( see \Modera\MJRCacheAwareClassLoaderBundle\DependencyInjection\Configuration )
 *  * if no version is configured using bundle semantic configuration then it will try to locate "modera-version.txt" file
 *    which is located one level above where AppKernel class resides
 *  * if neither of the ways worked out then a default "1.0.0" version will be returned
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class StandardVersionResolver implements VersionResolverInterface
{
    /* @var Kernel */
    private $kernel;
    private $semanticConfig = array();

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->kernel = $container->get('kernel');
        $this->semanticConfig = $container->getParameter(ModeraMJRCacheAwareClassLoaderExtension::CONFIG_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve()
    {
        $configuredVersion = isset($this->semanticConfig['version']) ? $this->semanticConfig['version'] : '';
        $fileVersion = @file_get_contents($this->kernel->getRootDir().'/../modera-version.txt');

        if ('' != $configuredVersion) {
            return $configuredVersion;
        } elseif (false !== $fileVersion) {
            return $fileVersion;
        } else {
            return '1.0.0';
        }
    }
}
