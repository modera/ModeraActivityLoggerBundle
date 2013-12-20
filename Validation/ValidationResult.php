<?php

namespace Modera\AdminGeneratorBundle\Validation;

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
        return array(
            'field_errors' => $this->fieldErrors,
            'general_errors' => $this->generalErrors
        );
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return count($this->toArray(), true) > 2;
    }

    static public function clazz()
    {
        return get_called_class();
    }
}