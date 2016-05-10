<?php

namespace Modera\ServerCrudBundle\Exceptions;

/**
 * This exception can be thrown when an invalid request is received from client-side - when it doesn't have some mandatory
 * parameters, for instance.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class BadRequestException extends \RuntimeException
{
    private $params = array();
    private $path;

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}
