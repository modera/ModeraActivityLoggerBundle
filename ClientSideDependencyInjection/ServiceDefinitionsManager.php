<?php

namespace Modera\MjrIntegrationBundle\ClientSideDependencyInjection;

use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * Provides an access to service side dependency injection container service definitions.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class ServiceDefinitionsManager
{
    private $provider;

    /**
     * @param ContributorInterface $provider
     */
    public function __construct(ContributorInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @return array
     */
    public function getDefinitions()
    {
        return $this->provider->getItems();
    }

    /**
     * @param string $id
     *
     * @return array|null
     */
    public function getDefinition($id)
    {
        $definitions = $this->getDefinitions();

        return isset($definitions[$id]) ? $definitions[$id] : null;
    }
}
