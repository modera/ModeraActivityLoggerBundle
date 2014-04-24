<?php

namespace Modera\ConfigBundle\Config;

use Modera\ConfigBundle\Entity\ConfigurationEntry;

/**
 * Exposes two configuration properties:
 * - true_text
 * - false_text
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class BooleanHandler implements HandlerInterface
{
    /**
     * @inheritDoc
     */
    public function getReadableValue(ConfigurationEntry $entry)
    {
        $cfg = $entry->getServerHandlerConfig();

        $trueValue = isset($cfg['true_text']) ? $cfg['true_text'] : 'true';
        $falseValue = isset($cfg['false_text']) ? $cfg['false_text'] : 'false';

        return $entry->getDenormalizedValue() == 1 ? $trueValue : $falseValue;
    }

    /**
     * @inheritDoc
     */
    public function getValue(ConfigurationEntry $entry)
    {
        return $entry->getDenormalizedValue();
    }

    /**
     * @inheritDoc
     */
    public function convertToStorageValue($object, ConfigurationEntry $entry)
    {
        return $object;
    }
}