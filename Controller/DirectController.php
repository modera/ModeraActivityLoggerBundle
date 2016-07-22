<?php

namespace Modera\DirectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Modera\DirectBundle\Api\Api;
use Modera\DirectBundle\Router\Router;

class DirectController extends Controller
{
    /**
     * Generate the ExtDirect API.
     * 
     * @return Response
     */
    public function getApiAction()
    {
        // instantiate the api object
        $api = new Api($this->container);

        $debug = $this->container->get('kernel')->isDebug();

        if ($debug) {
            $exceptionLogStr =
                'Ext.direct.Manager.on("exception", function(error){console.error(Ext.util.Format.format("Remote Call: {0}.{1}\n{2}", error.action, error.method, error.message, error.where)); return false;});';
        } else {
            $exceptionLogStr =
                sprintf('Ext.direct.Manager.on("exception", function(error){console.error("%s");});', $this->container->getParameter('direct.exception.message'));
        }
        // create the response
        $response = new Response(sprintf('Ext.Direct.addProvider(%s);%s', $api, $exceptionLogStr));
        $response->headers->set('Content-Type', 'text/javascript');

        return $response;
    }

    /**
     * Generate the Remoting ExtDirect API.
     *
     * @return Response
     */
    public function getRemotingAction()
    {
        // instantiate the api object
        $api = new Api($this->container);

        $debug = $this->container->get('kernel')->isDebug();

        // create the response
        $response = new Response(sprintf('Ext.app.REMOTING_API = %s;', $api));
        $response->headers->set('Content-Type', 'text/javascript');

        return $response;
    }

    /**
     * Route the ExtDirect calls.
     *
     * @return Response
     */
    public function routeAction()
    {
        // instantiate the router object
        $router = new Router($this->container);

        // create response
        $response = new Response($router->route());
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
