<?php

namespace Modera\ServerCrudBundle\Contributions;

use Modera\ServerCrudBundle\Security\SecurityControllerActionsInterceptor;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ControllerActionInterceptorsProvider implements ContributorInterface
{
    private $securityContext;
    private $items;

    public function __construct(ContainerInterface $container)
    {
        $this->securityContext = $container->get('security.context');
    }

    /**
     * @inheritDoc
     */
    public function getItems()
    {
        if (!$this->items) {
            $this->items = array(
                new SecurityControllerActionsInterceptor($this->securityContext)
            );
        }

        return $this->items;
    }
} 