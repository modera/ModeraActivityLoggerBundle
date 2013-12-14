<?php

namespace Modera\AdminGeneratorBundle\Generation;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class GenerationResult
{
    private $sourceCode;
    private $className;

    /**
     * @param string $sourceCode
     * @param string $className
     */
    public function __construct($sourceCode, $className)
    {
        $this->sourceCode = $sourceCode;
        $this->className = $className;
    }

    /**
     * @return string
     */
    public function getSourceCode()
    {
        return $this->sourceCode;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }
}