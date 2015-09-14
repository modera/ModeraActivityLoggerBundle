<?php

namespace Modera\MJRSecurityIntegrationBundle\Contributions;

use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Modera\MjrIntegrationBundle\Config\CallbackConfigMerger;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ConfigMergersProvider implements ContributorInterface
{
    private $items;

    public function __construct(SecurityContextInterface $sc, ContributorInterface $clientDiDefinitionsProvider)
    {
        $this->items = array(
            new CallbackConfigMerger(function(array $currentConfig) use ($sc) {
                if ($sc->getToken()) {
                    $roles = array();

                    foreach ($sc->getToken()->getRoles() as $role) {
                        $roles[] = $role->getRole();
                    }

                    return array_merge($currentConfig, array(
                        'roles' => $roles
                    ));
                } else {
                    return $currentConfig;
                }
            }),
            new CallbackConfigMerger(function(array $currentConfig) use ($clientDiDefinitionsProvider) {
                return array_merge($currentConfig, array(
                    'serviceDefinitions' => $clientDiDefinitionsProvider->getItems()
                ));
            })
        );
    }

    /**
     * @inheritDoc
     */
    public function getItems()
    {
        return $this->items;
    }
}