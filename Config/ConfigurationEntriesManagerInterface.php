<?php

namespace Modera\ConfigBundle\Config;

/**
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
     * @param ConfigurationEntryInterface $entry
     */
    public function save(ConfigurationEntryInterface $entry);
}
