<?php

namespace Modera\SecurityAwareJSRuntimeBundle\Contributions;

use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Modera\JSRuntimeIntegrationBundle\Config\CallbackConfigMerger;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ConfigMergersProvider implements ContributorInterface
{
    private $items;

    public function __construct(SecurityContextInterface $sc)
    {
        $this->items = array(
            new CallbackConfigMerger(function(array $currentConfig) use ($sc) {/
                if ($sc->getToken()) {
                    $roles = array();

                    foreach ($sc->getToken()->getRoles() as $role) {
                        $roles[] = $role->getRole();
                    }

                    return array_merge($currentConfig, array(
                        'userRoles' => $roles
                    ));
                } else {
                    return $currentConfig;
                }
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