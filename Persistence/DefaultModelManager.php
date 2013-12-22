<?php

namespace Modera\AdminGeneratorBundle\Persistence;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class DefaultModelManager implements ModelManagerInterface
{
    private function underscorizeWord($word)
    {
        $result = strtolower($word{0});
        for ($i=1; $i<strlen($word); $i++) {
            $char = $word{$i};

            if (strtoupper($char) === $char) {
                $result .= '_';
            }

            $result .= strtolower($char);
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function generateModelIdFromEntityClass($entityClass)
    {
        $result = array();

        foreach (explode('\\', $entityClass) as $segment) {
            if ('Entity' == $segment) {
                continue;
            }

            $result[] = $this->underscorizeWord($segment);
        }

        return implode('.', $result);
    }

    /**
     * @inheritDoc
     */
    public function generateEntityClassFromModelId($modelId)
    {
        // TODO

        $result = array();

        foreach (explode('.', $modelId) as $segment) {

        }

        return implode('\\', $result);
    }
}