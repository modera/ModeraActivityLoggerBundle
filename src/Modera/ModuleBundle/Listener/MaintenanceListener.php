<?php

namespace Modera\ModuleBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        if ($maintenance && !$debug) {
            $request = $event->getRequest();
            if ($request->isXmlHttpRequest()) {
                $result = array(
                    'success' => false,
                    'message' => 'The server is temporarily down for maintenance.',
                );
                $response = new JsonResponse($result);
            } else {
                $engine = $this->container->get('templating');
                $content = $engine->render('ModeraModuleBundle::maintenance.html.twig');
                $response = new Response($content, 503);
            }

            $event->setResponse($response);
            $event->stopPropagation();
        }
    }
}
