<?php

namespace Modera\BackendModuleBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class MaintenanceListener
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $maintenance = false;
        if ($this->container->hasParameter('maintenance')) {
            $maintenance = $this->container->getParameter('maintenance');
        }

        $debug = in_array($this->container->get('kernel')->getEnvironment(), array('test', 'dev'));
        $debug = false;

        if ($maintenance && !$debug) {
            $engine = $this->container->get('templating');
            $content = $engine->render('ModeraBackendModuleBundle::maintenance.html.twig');
            $event->setResponse(new Response($content, 503));
            $event->stopPropagation();
        }

    }
}
