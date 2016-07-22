<?php

namespace Modera\ConfigBundle\Config;

/**
 * @deprecated Use \Modera\ConfigBundle\Manager\ConfigurationEntriesManagerInterface instead
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
interface ConfigurationEntriesManagerInterface
{
    /**
     * @param string $name
     *
     * @return ConfigurationEntryInterface
     */
    public function findOneByName($name);

    /**
     * @throws \RuntimeException
     *
     * @param string $name
     *
     * @return ConfigurationEntryInterface
     */
    public function findOneByNameOrDie($name);

    /**
     * @throws \Modera\ConfigBundle\Manager\ConfigurationEntryAlreadyExistsException
     *
     * @param ConfigurationEntryInterface $entry
     */
    public function save(ConfigurationEntryInterface $entry);
}
