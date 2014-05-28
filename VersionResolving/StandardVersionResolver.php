<?php

namespace Modera\MJRCacheAwareClassLoaderBundle\VersionResolving;

use Modera\MJRCacheAwareClassLoaderBundle\DependencyInjection\ModeraMJRCacheAwareClassLoaderExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
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
     * @inheritDoc
     */
    public function resolve()
    {
        $configuredVersion = isset($this->semanticConfig['version']) ? $this->semanticConfig['version'] : '';
        $fileVersion = @file_get_contents($this->kernel->getRootDir() . '/../modera-version.txt');

        if ('' != $configuredVersion) {
            return $configuredVersion;
        } else if (false !== $fileVersion) {
            return $fileVersion;
        } else {
            return '1.0.0';
        }
    }
} 