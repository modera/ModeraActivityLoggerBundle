<?php

namespace Modera\MjrIntegrationBundle\Contributions;

use Modera\MjrIntegrationBundle\DependencyInjection\ModeraJSRuntimeIntegrationExtension;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class JsResourcesProvider implements ContributorInterface
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var array
     */
    private $bundleConfig;

    private $isDevEnv;

    /**
     * @param Router $router
     */
    public function __construct(ContainerInterface $container)
    {
        $this->router = $container->get('router');
        $this->bundleConfig = $container->getParameter(ModeraJSRuntimeIntegrationExtension::CONFIG_KEY);

        /* @var Kernel $kernel */
        $kernel = $container->get('kernel');
        $this->isDevEnv = $kernel->getEnvironment() == 'dev';
    }

    /**
     * @inheritDoc
     */
    public function getItems()
    {
        $extjs = $this->bundleConfig['extjs_path'] . '/ext-all' . ($this->isDevEnv ? '-debug-w-comments' : '') . '.js';

        return array(
            $extjs,
            $this->router->generate('api'),
            '//cdnjs.cloudflare.com/ajax/libs/moment.js/2.4.0/moment-with-langs.min.js',
            $this->router->generate('mf_font_awesome'),
            '/bundles/moderajsruntimeintegration/js/orientationchange.js',
        );
    }
}