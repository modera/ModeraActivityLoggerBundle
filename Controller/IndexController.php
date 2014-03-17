<?php

namespace Modera\SecurityAwareJSRuntimeBundle\Controller;

use Modera\JSRuntimeIntegrationBundle\ClientSideDependencyInjection\ServiceDefinitionsManager;
use Modera\JSRuntimeIntegrationBundle\DependencyInjection\ModeraJSRuntimeIntegrationExtension;
use Modera\SecurityAwareJSRuntimeBundle\DependencyInjection\ModeraSecurityAwareJSRuntimeExtension;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

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

        return [
            'config' => array_merge($runtimeConfig, $securedRuntimeConfig),
            'css_resources' => $cssResourcesProvider->getItems(),
            'js_resources' => $jsResourcesProvider->getItems()
        ];
    }

    /**
     * @Route("app.js")
     * @Template
     */
    public function applicationAction()
    {
        /* @var ServiceDefinitionsManager $definitionsMgr */
        $definitionsMgr = $this->container->get('mf.jsruntimeintegrationbundle.csdi.service_definitions_manager');

        return array(
            'container_services' => $definitionsMgr->getDefinitions(),
        );
    }
}