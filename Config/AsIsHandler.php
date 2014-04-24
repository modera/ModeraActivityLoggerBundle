<?php

namespace Modera\ConfigBundle\Config;

use Modera\ConfigBundle\Entity\ConfigurationEntry;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class AsIsHandler implements HandlerInterface
{
    /**
     * @inheritDoc
     */
    public function getReadableValue(ConfigurationEntry $entry)
    {
        return $entry->getDenormalizedValue();
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
