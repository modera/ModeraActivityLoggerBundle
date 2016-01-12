<?php

namespace Modera\ConfigBundle\Config;

use Modera\ConfigBundle\Entity\ConfigurationEntry;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
interface HandlerInterface
{
    /**
     * @return string Value that will be displayed in the frontend (list view).
     */
    public function getReadableValue(ConfigurationEntry $entry);

    /**
     * @return mixed Oftentimes value stored in {@class ConfigurationEntry} will be some entity
     *               primary key and your handler will use it to return an entity.
     */
    public function getValue(ConfigurationEntry $entry);

    /**
     * Takes a value (it can be an object or whatever) that came from client side(or from some other place) and converts
     * it to something that can be saved in database.
     *
     * @param mixed              $object
     * @param ConfigurationEntry $entry
     *
     * @return mixed
     */
    public function convertToStorageValue($object, ConfigurationEntry $entry);
}
