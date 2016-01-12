<?php

namespace Modera\ConfigBundle\Config;

use Modera\ConfigBundle\Entity\ConfigurationEntry;

/**
 * This exception will thrown when some required configuration parameters for
 * {@class EntityRepositoryHandler} are not provided.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class MissingConfigurationParameterException extends \RuntimeException
{
    private $parameter;

    public function setParameter($parameter)
    {
        $this->parameter = $parameter;
    }

    public function getParameter()
    {
        return $this->parameter;
    }

    /**
     * @param \Modera\ConfigBundle\Entity\ConfigurationEntry $entry
     * @param string                                         $parameter
     *
     * @return MissingConfigurationParameterException
     */
    public static function create(ConfigurationEntry $entry, $parameter)
    {
        $me = new self(sprintf(
            '%s::getServerHandlerConfig(): configuration property "%s" for ConfigurationEntry with id "%s" is not provided!',
            get_class($entry), $parameter, $entry->getId()
        ));
        $me->setParameter($parameter);

        return $me;
    }
}
