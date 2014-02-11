<?php

namespace Modera\JSRuntimeIntegrationBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Modera\JSRuntimeIntegrationBundle\Config\ConfigManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Modera\JSRuntimeIntegrationBundle\Model\FontAwesome;

/**
 * Exposes actions which can be used by client-side runtime to configure/manage its state.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2013 Modera Foundation
 */
class IndexController extends Controller
{
    /**
     * TODO must be secured
     *
     * @Route("/get-config", name="mf_get_config")
     * @return array
     */
    public function getConfigAction()
    {
        /* @var ConfigManager $configManager */
        $configManager = $this->get('mf.jsruntimeintegration.config.config_manager');

        return new Response(json_encode($configManager->getConfig(), \JSON_PRETTY_PRINT));
    }

    /**
     * @Route("/font-awesome.js", name="mf_font_awesome")
     */
    public function fontAwesomeAction()
    {
        $response = new Response(FontAwesome::jsCode());
        $response->headers->set('Content-Type', 'text/javascript');

        return $response;
    }
}