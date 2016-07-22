<?php

namespace Modera\DirectBundle\Router;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request
{
    /**
     * The Symfony request object taked by ModeraDirectBundle controller.
     *
     * @var Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * The HTTP_RAW_POST_DATA if the Direct call is a batch call.
     *
     * @var JSON
     */
    protected $rawPost;

    /**
     * The $_POST data if the Direct Call is a form call.
     *
     * @var array
     */
    protected $post;

    /**
     * Store the Direct Call type. Where values in ('form','batch').
     *
     * @var string
     */
    protected $callType;

    /**
     * Is upload request?
     *
     * @var bool
     */
    protected $isUpload = false;

    /**
     * Store the Direct calls. Only 1 if it a form call or 1.* if it a
     * batch call.
     *
     * @var array
     */
    protected $calls = null;

    /**
     * Store the $_FILES if it a form call.
     *
     * @var array
     */
    protected $files;

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct(SymfonyRequest $request)
    {
        // before we were checking if $request->request->count() > 0 to
        // check if a given request is a form submission but in some cases
        // (Symfony?) merges all parameters (event decoded json body) into
        // $request->request and it was causing problems. Hopefully this solution
        // will work in all cases
        $hasJsonInBody = is_array(json_decode($request->getContent(), true));

        $this->request = $request;
        $this->rawPost = $request->getContent() ? $request->getContent() : array();
        $this->post = $request->request->all();
        $this->files = $request->files->all();
        $this->callType = $hasJsonInBody ? 'batch' : 'form';
        $this->isUpload = $request->request->get('extUpload') == 'true';
    }

    /**
     * Return the type of Direct call.
     *
     * @return string
     */
    public function getCallType()
    {
        return $this->callType;
    }

    /**
     * Is upload request?
     *
     * @return bool
     */
    public function isUpload()
    {
        return $this->isUpload;
    }

    /**
     * Return the files from call.
     *
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Get the direct calls object.
     *
     * @return array
     */
    public function getCalls()
    {
        if (null == $this->calls) {
            $this->calls = $this->extractCalls();
        }

        return $this->calls;
    }

    /**
     * Extract the ExtDirect calls from request.
     *
     * @return array
     */
    public function extractCalls()
    {
        $calls = array();

        if ('form' == $this->callType) {
            $calls[] = new Call($this->post, 'form');
        } else {
            $decoded = json_decode($this->rawPost);
            $decoded = !is_array($decoded) ? array($decoded) : $decoded;

            array_walk_recursive($decoded, array($this, 'parseRawToArray'));
            // @todo: check utf8 config option from bundle
            //array_walk_recursive($decoded, array($this, 'decode'));

            foreach ($decoded as $call) {
                $calls[] = new Call((array) $call, 'single');
            }
        }

        return $calls;
    }

    /**
     * Force the utf8 decodification from all string values.
     *
     * @param mixed  $value
     * @param string $key
     */
    public function decode(&$value, &$key)
    {
        if (is_string($value)) {
            $value = utf8_decode($value);
        }
    }

    /**
     * Parse a raw http post to a php array.
     *
     * @param mixed  $value
     * @param string $key
     */
    private function parseRawToArray(&$value, &$key)
    {
        // parse a json string to an array
        if (is_string($value)) {
            $pos = substr($value, 0, 1);
            if ($pos == '[' || $pos == '(' || $pos == '{') {
                $json = json_decode($value);
            } else {
                $json = $value;
            }

            if ($json) {
                $value = $json;
            }
        }

        // if the value is an object, parse it to an array
        if (is_object($value)) {
            $value = (array) $value;
        }

        // call the recursive function to all keys of array
        if (is_array($value)) {
            array_walk_recursive($value, array($this, 'parseRawToArray'));
        }
    }
}
