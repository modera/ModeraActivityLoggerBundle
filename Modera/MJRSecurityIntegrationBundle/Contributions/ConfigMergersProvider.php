<?php

namespace Modera\MJRSecurityIntegrationBundle\Contributions;

use Modera\SecurityBundle\Security\Authenticator;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Modera\MjrIntegrationBundle\Config\CallbackConfigMerger;

/**
 * Provides runtime configuration which should become available after user has authenticated.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ConfigMergersProvider implements ContributorInterface
{
    /**
     * @var array
     */
    private $items;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param ContributorInterface  $clientDiDefinitionsProvider
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        ContributorInterface $clientDiDefinitionsProvider
    ) {
        $this->items = array(
            new CallbackConfigMerger(function (array $currentConfig) use ($tokenStorage) {
                // we are not making sure that user is authenticated here because we expect that this
                // callback is invoked only when user is already authenticated (invoked from behind a firewall)
                if ($token = $tokenStorage->getToken()) {
                    $roles = array();

                    foreach ($token->getRoles() as $role) {
                        $roles[] = $role->getRole();
                    }

                    return array_merge($currentConfig, array(
                        'roles' => $roles,
                        'userProfile' => Authenticator::userToArray($token->getUser()),
                    ));
                } else {
                    return $currentConfig;
                }
            }),
            new CallbackConfigMerger(function (array $currentConfig) use ($clientDiDefinitionsProvider) {
                return array_merge($currentConfig, array(
                    'serviceDefinitions' => $clientDiDefinitionsProvider->getItems(),
                ));
            }),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return $this->items;
    }
}
