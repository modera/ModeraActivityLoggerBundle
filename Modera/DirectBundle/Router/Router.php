<?php

namespace Modera\DirectBundle\Router;

use Symfony\Component\DependencyInjection\ContainerAware;
use Modera\DirectBundle\Api\ControllerApi;

class Router
{
    /**
     * The ExtDirect Request object.
     * 
     * @var Modera\DirectBundle\Request
     */
    protected $request;

    /**
     * The ExtDirect Response object.
     * 
     * @var Modera\DirectBundle\Response
     */
    protected $response;

    /**
     * The application container.
     * 
     * @var Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    /**
     * Initialize the router object.
     * 
     * @param Container $container
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->request = new Request($container->get('request'));
        $this->response = new Response($this->request->getCallType(), $this->request->isUpload());
        $this->defaultAccess = $container->getParameter('direct.api.default_access');
        $this->session = $this->container->get('session')->get($container->getParameter('direct.api.session_attribute'));
    }

    /**
     * Do the ExtDirect routing processing.
     *
     * @return JSON
     */
    public function route()
    {
        $batch = array();

        foreach ($this->request->getCalls() as $call) {
            $batch[] = $this->dispatch($call);
        }

        return $this->response->encode($batch);
    }

    /**
     * Dispatch a remote method call.
     * 
     * @param Modera\DirectBundle\Router\Call $call
     *
     * @return mixed
     */
    private function dispatch($call)
    {
        $api = new ControllerApi($this->container, $this->getControllerClass($call->getAction()));

        $controller = $this->resolveController($call->getAction());
        $method = $call->getMethod().'Action';
        $accessType = $api->getMethodAccess($method);

        if (!is_callable(array($controller, $method))) {
            //todo: throw an execption method not callable
            return false;
        } elseif ($this->defaultAccess == 'secure' && $accessType != 'anonymous') {
            if (!$this->session) {
                $result = $call->getException(new \Exception('Access denied!'));
            }
        } elseif ($accessType == 'secure') {
            if (!$this->session) {
                $result = $call->getException(new \Exception('Access denied!'));
            }
        } elseif ('form' == $this->request->getCallType()) {
            $result = $call->getResponse($controller->$method($call->getData(), $this->request->getFiles()));
        }

        if (!isset($result)) {
            try {
                //$result = call_user_func_array(array($controller, $method), $call->getData());
                $result = $controller->$method($call->getData());
                $result = $call->getResponse($result);
            } catch (\Exception $e) {
                $result = $call->getException($e);
            }
        }

        return $result;
    }

    /**
     * Resolve the called controller from action.
     * 
     * @param string $action
     *
     * @return <type>
     */
    private function resolveController($action)
    {
        $class = $this->getControllerClass($action);

        try {
            $controller = new $class();

            if ($controller instanceof ContainerAware) {
                $controller->setContainer($this->container);
            }

            return $controller;
        } catch (Exception $e) {
            // todo: handle exception
        }
    }

    /**
     * Return the controller class name.
     *
     * @param $action
     */
    private function getControllerClass($action)
    {
        list($bundleName, $controllerName) = explode('_', $action);
        $bundleName .= 'Bundle';

        $bundle = $this->container->get('kernel')->getBundle($bundleName);
        $namespace = $bundle->getNamespace().'\\Controller';

        $class = $namespace.'\\'.$controllerName.'Controller';

        return $class;
    }
}
