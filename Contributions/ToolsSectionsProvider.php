<?php

namespace Modera\BackendSecurityBundle\Contributions;

use Modera\BackendSecurityBundle\ModeraBackendSecurityBundle;
use Modera\BackendToolsBundle\Section\Section;
use Modera\FoundationBundle\Translation\T;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Contributes a section to Backend/Tools
 *
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ToolsSectionsProvider implements ContributorInterface
{
    private $securityContext;

    private $items;

    /**
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * @inheritDoc
     */
    public function getItems()
    {
        if (!$this->items) {
            $this->items = array();

            if ($this->securityContext->isGranted(ModeraBackendSecurityBundle::ROLE_ACCESS_BACKEND_TOOLS_SECURITY_SECTION)) {
                $this->items[] = new Section(
                    T::trans('Security permissions'),
                    'tools.security',
                    T::trans('Control permissions of users/groups.'),
                    '', '',
                    'modera-backend-security-tools-icon'
                );
            }
        }

        return $this->items;
    }
}