<?php

namespace Modera\SecurityAwareJSRuntimeBundle\Controller;

use Modera\JSRuntimeIntegrationBundle\ClientSideDependencyInjection\ServiceDefinitionsManager;
use Modera\JSRuntimeIntegrationBundle\DependencyInjection\ModeraJSRuntimeIntegrationExtension;
use Modera\SecurityAwareJSRuntimeBundle\DependencyInjection\ModeraSecurityAwareJSRuntimeExtension;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\RouterInterface;

/**
 * Entry point to web application.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class IndexController extends Controller
{
    /**
     * @Route("/")
     * @Template
     * @return array
     */
    public function indexAction()
    {
        $runtimeConfig = $this->container->getParameter(ModeraJSRuntimeIntegrationExtension::CONFIG_KEY);
        $securedRuntimeConfig = $this->container->getParameter(ModeraSecurityAwareJSRuntimeExtension::CONFIG_KEY);

        /* @var ContributorInterface $cssResourcesProvider */
        $cssResourcesProvider = $this->get('mf.jsruntimeintegration.css_resources_provider');

        /* @var ContributorInterface $cssResourcesProvider */
        $jsResourcesProvider = $this->get('mf.jsruntimeintegration.js_resources_provider');

        /* @var RouterInterface $router */
        $router = $this->get('router');
        // converting URL like /app_dev.php/backend/ModeraFoundation/Application.js to /app_dev.php/backend/ModeraFoundation
        $appLoadingPath = $router->generate('modera_security_aware_js_runtime.index.application');
        $appLoadingPath = substr($appLoadingPath, 0, strpos($appLoadingPath, 'Application.js') - 1);

        return array(
            'config' => array_merge($runtimeConfig, $securedRuntimeConfig),
            'css_resources' => $cssResourcesProvider->getItems(),
            'js_resources' => $jsResourcesProvider->getItems(),
            'app_loading_path' => $appLoadingPath
        );
    }

    /**
     * Dynamically generates an entry point to backend application.
     *
     * @see Resources/config/routing.yml
     * @see \Modera\SecurityAwareJSRuntimeBundle\Contributions\RoutingResourcesProvider
     *
     * @Template
     */
    public function applicationAction()
    {
        /* @var ServiceDefinitionsManager $definitionsMgr */
        $definitionsMgr = $this->container->get('mf.jsruntimeintegrationbundle.csdi.service_definitions_manager');

        return array(
            'container_services' => $definitionsMgr->getDefinitions(),
            'config' => $this->container->getParameter(ModeraJSRuntimeIntegrationExtension::CONFIG_KEY)
        );
    }
}