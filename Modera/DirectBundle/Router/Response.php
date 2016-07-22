<?php

namespace Modera\DirectBundle\Router;

class Response
{
    /**
     * Call type to respond. Where values in ('form','single).
     *   
     * @var string
     */
    protected $type;

    /**
     * Is upload request?
     * 
     * @var bool
     */
    protected $isUpload = false;

    /**
     * Initialize the object setting it type.
     * 
     * @param string $type
     * @param bool   $isUpload
     */
    public function __construct($type, $isUpload)
    {
        $this->type = $type;
        $this->isUpload = $isUpload;
    }

    /**
     * Encode the response into a valid json ExtDirect result.
     * 
     * @param array $result
     *
     * @return string
     */
    public function encode($result)
    {
        if ($this->type == 'form' && $this->isUpload) {
            //array_walk_recursive($result[0], array($this, 'utf8'));
            return '<html><body><textarea>'.json_encode($result[0]).'</textarea></body></html>';
        } else {
            // @todo: check utf8 config option from bundle
            //array_walk_recursive($result, array($this, 'utf8'));
            return json_encode($result);
        }
    }

    /**
     * Encode the result recursivily as utf8.
     *
     * @param mixed  $value
     * @param string $key
     */
    private function utf8(&$value, &$key)
    {
        if (is_string($value)) {
            $value = utf8_encode($value);
        }

        if (is_array($value)) {
            array_walk_recursive($value, array($this, 'utf8'));
        }
    }
}
