<?php

namespace Modera\ConfigBundle\Manager;

use Modera\ConfigBundle\Config\ConfigurationEntryInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
interface ConfigurationEntriesManagerInterface extends \Modera\ConfigBundle\Config\ConfigurationEntriesManagerInterface
{
    /**
     * @param string $name
     * @param object $owner
     *
     * @return ConfigurationEntryInterface
     */
    public function findOneByName($name, $owner = null);

    /**
     * @throws \RuntimeException
     *
     * @param string $name
     * @param object $owner
     *
     * @return ConfigurationEntryInterface
     */
    public function findOneByNameOrDie($name, $owner = null);
}
