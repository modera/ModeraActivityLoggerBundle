<?php

namespace Modera\BackendSecurityBundle\Contributions;

use Modera\BackendSecurityBundle\ModeraBackendSecurityBundle;
use Modera\BackendToolsBundle\Section\Section;
use Modera\FoundationBundle\Translation\T;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Contributes a section to Backend/Tools.
 *
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ToolsSectionsProvider implements ContributorInterface
{
    private $authorizationChecker;

    private $items;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        if (!$this->items) {
            $this->items = array();

            if ($this->authorizationChecker->isGranted(ModeraBackendSecurityBundle::ROLE_ACCESS_BACKEND_TOOLS_SECURITY_SECTION)) {
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
