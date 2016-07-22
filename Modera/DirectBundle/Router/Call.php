<?php

namespace Modera\DirectBundle\Router;

class Call
{
    /**
     * The ExtDirect action called. With reference to Bundle via underscore '_'.
     * 
     * @var string
     */
    protected $action;

    /**
     * The ExtDirect method called.
     * 
     * @var string
     */
    protected $method;

    /**
     * The ExtDirect request type.
     * 
     * @var string
     */
    protected $type;

    /**
     * The ExtDirect transaction id.
     * 
     * @var int
     */
    protected $tid;

    /**
     * The ExtDirect call params.
     * 
     * @var array
     */
    protected $data;

    /**
     * The ExtDirect request type. Where values in ('form','single').
     * 
     * @var string
     */
    protected $callType;

    /**
     * The ExtDirect upload reference.
     * 
     * @var bool
     */
    protected $upload;

    /**
     * Initialize an ExtDirect call.
     * 
     * @param array  $call
     * @param string $type
     */
    public function __construct($call, $type)
    {
        $this->callType = $type;

        if ('single' == $type) {
            $this->initializeFromSingle($call);
        } else {
            $this->initializeFromForm($call);
        }
    }

    /**
     * Get the requested action.
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get the requested method.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get the request method params.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Return a result wrapper to ExtDirect method call.
     * 
     * @param array $result
     *
     * @return array
     */
    public function getResponse($result)
    {
        return array(
          'type' => 'rpc',
          'tid' => $this->tid,
          'action' => $this->action,
          'method' => $this->method,
          'result' => $result,
        );
    }

    /**
     * Return an exception to ExtDirect call stack.
     * 
     * @param \Exception $exception
     *
     * @return array
     */
    public function getException($exception)
    {
        return array(
            'type' => 'exception',
            'class' => get_class($exception),
            'tid' => $this->tid,
            'action' => $this->action,
            'method' => $this->method,
            'message' => $exception->getMessage(),
            'where' => $exception->getTraceAsString(),
        );
    }

    /**
     * Initialize the call properties from a single call.
     * 
     * @param array $call
     */
    private function initializeFromSingle($call)
    {
        $this->action = $call['action'];
        $this->method = $call['method'];
        $this->type = $call['type'];
        $this->tid = $call['tid'];
        $this->data = (array) $call['data'][0];
    }

    /**
     * Initialize the call properties from a form call.
     * 
     * @param array $call
     */
    private function initializeFromForm($call)
    {
        $this->action = $call['extAction'];
        unset($call['extAction']);
        $this->method = $call['extMethod'];
        unset($call['extMethod']);
        $this->type = $call['extType'];
        unset($call['extType']);
        $this->tid = $call['extTID'];
        unset($call['extTID']);
        $this->upload = $call['extUpload'];
        unset($call['extUpload']);

        foreach ($call as $key => $value) {
            $this->data[$key] = $value;
        }
    }
}
