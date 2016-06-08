<?php

namespace Modera\BackendSecurityBundle\Controller;

use Modera\BackendSecurityBundle\ModeraBackendSecurityBundle;
use Modera\SecurityBundle\Entity\Group;
use Modera\ServerCrudBundle\Controller\AbstractCrudController;
use Modera\FoundationBundle\Translation\T;
use Modera\ServerCrudBundle\DataMapping\DataMapperInterface;
use Modera\ServerCrudBundle\NewValuesFactory\NewValuesFactoryInterface;
use Modera\ServerCrudBundle\Validation\DefaultEntityValidator;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class GroupsController extends AbstractCrudController
{
    /**
     * @return array
     */
    public function getConfig()
    {
        $em = $this->getDoctrine();

        $groupEntityValidator = function (array $params, Group $group, DefaultEntityValidator $defaultValidator, array $config, ContainerInterface $container) use ($em) {

            $validationResult = $defaultValidator->validate($group, $config);

            if (!$group->getRefName()) {
                return $validationResult;
            }

            /** @var Group[] $groupWithSuchRefNameList */
            $groupWithSuchRefNameList = $em->getRepository(Group::clazz())->findByRefName($group->getRefName());

            if (count($groupWithSuchRefNameList) > 0) {
                $groupWithSuchRefName = $groupWithSuchRefNameList[0];

                if ($groupWithSuchRefName->getId() != $group->getId()) {
                    $validationResult->addFieldError(
                        'refName',
                        T::trans(
                            'This refName is taken. Consider use \'%groupName%\' group or change current reference name.',
                            array('%groupName%' => $groupWithSuchRefName->getName())
                        )
                    );
                }
            }

            return $validationResult;

        };

        $mapEntity = function (array $params, Group $group, DataMapperInterface $defaultMapper, ContainerInterface $container) {
            $defaultMapper->mapData($params, $group);

            /*
             * Because of unique constrain we cannot save '' value as refName.
             * Only one time can, actually. :) So, to allow user use groups without
             * refName we have to set null by force because of ExtJs empty form value
             * is ''.
             */
            $refName = $group->getRefName();
            if ($refName === '') {
                $group->setRefName(null);
            } else {
                /*
                 * To help users avoid duplicates group we use normalizing for refName
                 */
                $group->setRefName(Group::normalizeRefNameString($refName));
            }

        };

        return array(
            'entity' => Group::clazz(),
            'security' => array(
                'role' => ModeraBackendSecurityBundle::ROLE_ACCESS_BACKEND_TOOLS_SECURITY_SECTION,
                'actions' => array(
                    'create' => ModeraBackendSecurityBundle::ROLE_MANAGE_PERMISSIONS,
                    'update' => ModeraBackendSecurityBundle::ROLE_MANAGE_PERMISSIONS,
                    'remove' => ModeraBackendSecurityBundle::ROLE_MANAGE_PERMISSIONS,
                ),
            ),
            'hydration' => array(
                'groups' => array(
                    'list' => function (Group $group) {
                        return array(
                            'id' => $group->getId(),
                            'name' => $group->getName(),
                            'usersCount' => count($group->getUsers()),
                        );
                    },
                    'delete-group' => ['name'],
                    'main-form' => ['id', 'name', 'refName'],
                    'compact-list' => ['id', 'name'],
                ),
                'profiles' => array(
                    'list', 'compact-list',
                    'delete-group',
                    'edit-group' => array('main-form'),
                ),
            ),
            'format_new_entity_values' => function (array $params, array $config, NewValuesFactoryInterface $defaultImpl, ContainerInterface $container) {
                return array(
                    'refName' => null,
                );
            },
            'new_entity_validator' => $groupEntityValidator,
            'updated_entity_validator' => $groupEntityValidator,
            'map_data_on_create' => $mapEntity,
            'map_data_on_update' => $mapEntity,
        );
    }
}
