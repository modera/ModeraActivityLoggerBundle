<?php

namespace Modera\ServerCrudBundle\Validation;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class ValidationResult
{
    private $fieldErrors = array();
    private $generalErrors = array();

    /**
     * @param string $fieldName
     * @param string $error
     */
    public function addFieldError($fieldName, $error)
    {
        if (!isset($this->fieldErrors[$fieldName])) {
            $this->fieldErrors[$fieldName] = array();
        }

        $this->fieldErrors[$fieldName][] = $error;
    }

    /**
     * @param string[]
     */
    public function addGeneralError($error)
    {
        $this->generalErrors[] = $error;
    }

    /**
     * @param string $fieldName
     *
     * @return array
     */
    public function getFieldErrors($fieldName)
    {
        return isset($this->fieldErrors[$fieldName]) ? $this->fieldErrors[$fieldName] : array();
    }

    /**
     * @return array
     */
    public function getGeneralErrors()
    {
        return $this->generalErrors;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = array();

        if (count($this->fieldErrors)) {
            $result['field_errors'] = $this->fieldErrors;
        }
        if (count($this->generalErrors)) {
            $result['general_errors'] = $this->generalErrors;
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        $array = $this->toArray();

        return isset($array['field_errors']) || isset($array['general_errors']);
    }

    static public function clazz()
    {
        return get_called_class();
    }
}