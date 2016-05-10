<?php

namespace Modera\ServerCrudBundle\Contributions;

use Modera\ServerCrudBundle\Security\SecurityControllerActionsInterceptor;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ControllerActionInterceptorsProvider implements ContributorInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;
    private $items;

    public function __construct(ContainerInterface $container)
    {
        $this->authorizationChecker = $container->get('security.authorization_checker');
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        if (!$this->items) {
            $this->items = array(
                new SecurityControllerActionsInterceptor($this->authorizationChecker),
            );
        }

        return $this->items;
    }
}
