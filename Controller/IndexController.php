<?php

namespace Modera\JSRuntimeIntegrationBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Modera\JSRuntimeIntegrationBundle\Config\ConfigManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Exposes actions which can be used by client-side runtime to configure/manage its state.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class IndexController extends Controller
{
    /**
     * @Route("/get-config", name="mf_get_config")
     */
    public function getConfigAction()
    {
        /* @var ConfigManager $configManager */
        $configManager = $this->get('mf.jsruntimeintegration.config.config_manager');

        return $configManager->getConfig();
    }
}