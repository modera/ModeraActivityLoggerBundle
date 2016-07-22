<?php

namespace Modera\ConfigBundle\Config;

use Modera\ConfigBundle\Entity\ConfigurationEntry;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class DictionaryHandler implements HandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getReadableValue(ConfigurationEntry $entry)
    {
        $cfg = $entry->getServerHandlerConfig();

        if (isset($cfg['dictionary']) && isset($cfg['dictionary'][$entry->getDenormalizedValue()])) {
            return $cfg['dictionary'][$entry->getDenormalizedValue()];
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(ConfigurationEntry $entry)
    {
        return $entry->getDenormalizedValue();
    }

    /**
     * {@inheritdoc}
     */
    public function convertToStorageValue($input, ConfigurationEntry $entry)
    {
        return $input;
    }
}
