<?php

namespace Modera\ServerCrudBundle\Validation;

/**
 * Class should be used to report validation errors.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class ValidationResult
{
    private $fieldErrors = array();
    private $generalErrors = array();

    /**
     * Adds a field related error.
     *
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
     * You can use this method to report some general error ( error that is associated with no fields or associated
     * to several ones at the same time and you don't want to show same error message for several fields ).
     *
     * @param string $error
     */
    public function addGeneralError($error)
    {
        $this->generalErrors[] = $error;
    }

    /**
     * @param string $fieldName
     *
     * @return string[]
     */
    public function getFieldErrors($fieldName)
    {
        return isset($this->fieldErrors[$fieldName]) ? $this->fieldErrors[$fieldName] : array();
    }

    /**
     * @return string[]
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

    public static function clazz()
    {
        return get_called_class();
    }
}
