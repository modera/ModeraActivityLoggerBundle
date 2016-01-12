<?php

namespace Modera\ConfigBundle\Config;

/**
 * It is highly advisable that outside of this bundle all components rely on this interface instead of currently used
 * implementation {@class Modera\ConfigBundle\Entity\ConfigurationEntry}.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
interface ConfigurationEntryInterface
{
    /**
     * @return string Unique name of configuration property
     */
    public function getName();

    /**
     * @return mixed A value {@class HandlerInterface} returned when converted denormalized value. Usually this will
     *               be something that you use in your business logic, like an entity.
     */
    public function getValue();

    /**
     * @param mixed $value Value passed to this method may be some complex structure, for example - an entity. If
     *                     an entity is passed, then implementation of {@class HandlerInterface} is responsible
     *                     to cast it down to something that can be stored in database.
     *
     * @return mixed
     */
    public function setValue($value);

    /**
     * @return mixed A value that can be understood by a javascript configuration class that will be used
     *               to update values for this configuration property.
     */
    public function getDenormalizedValue();

    /**
     * @param mixed $value
     *
     * @return int One of TYPE constants
     */
    public function setDenormalizedValue($value);

    /**
     * @return string A human-readable value of currently stored value. For example, this value will
     *                be used LIST view where all available configuration properties are displayed.
     */
    public function getReadableValue();

    /**
     * If returns TRUE then this configuration property will be accessible from web ( js-runtime ).
     *
     * @return bool
     */
    public function isExposed();

    /**
     * @return bool
     */
    public function isReadOnly();
}
