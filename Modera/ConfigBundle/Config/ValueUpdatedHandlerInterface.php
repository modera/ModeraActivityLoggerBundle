<?php

namespace Modera\ConfigBundle\Config;

/**
 * Implementations of this interface will have a chance to do some additional processing when configuration entry
 * is updated.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
interface ValueUpdatedHandlerInterface
{
    /**
     * This method will be invoked when new data has been already mapped to $entry but not persisted
     * to storage yet.
     *
     * @param ConfigurationEntryInterface $entry
     */
    public function onUpdate(ConfigurationEntryInterface $entry);
}
