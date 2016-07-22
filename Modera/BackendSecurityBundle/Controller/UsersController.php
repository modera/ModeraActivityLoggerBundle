<?php

namespace Modera\BackendSecurityBundle\Controller;

use Modera\BackendSecurityBundle\ModeraBackendSecurityBundle;
use Modera\SecurityBundle\Entity\User;
use Modera\SecurityBundle\Service\UserService;
use Modera\ServerCrudBundle\Controller\AbstractCrudController;
use Modera\ServerCrudBundle\DataMapping\DataMapperInterface;
use Modera\ServerCrudBundle\Hydration\HydrationProfile;
use Modera\ServerCrudBundle\Persistence\OperationResult;
use Modera\FoundationBundle\Translation\T;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Modera\BackendSecurityBundle\Service\MailService;
use Modera\DirectBundle\Annotation\Remote;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class UsersController extends AbstractCrudController
{
    /**
     * @return array
     */
    public function getConfig()
    {
        $self = $this;

        return array(
            'entity' => User::clazz(),
            'create_default_data_mapper' => function (ContainerInterface $container) {
                return $this->container->get('modera_backend_security.data_mapper.user_data_mapper');
            },
            'security' => array(
                'actions' => array(
                    'create' => ModeraBackendSecurityBundle::ROLE_MANAGE_USER_PROFILES,
                    'update' => function (AuthorizationCheckerInterface $ac, array $params) use ($self) {
                        /* @var TokenStorageInterface $ts */
                        $ts = $self->get('security.token_storage');
                        /* @var User $user */
                        $user = $ts->getToken()->getUser();

                        if ($ac->isGranted(ModeraBackendSecurityBundle::ROLE_MANAGE_USER_PROFILES)) {
                            return true;
                        } else {
                            // irrespectively of what privileges user has we will always allow him to edit his
                            // own profile data
                            return $user instanceof User && isset($params['record']['id'])
                                   && $user->getId() == $params['record']['id'];
                        }
                    },
                    'remove' => ModeraBackendSecurityBundle::ROLE_MANAGE_USER_PROFILES,
                    'list' => ModeraBackendSecurityBundle::ROLE_ACCESS_BACKEND_TOOLS_SECURITY_SECTION,
                ),
            ),
            'hydration' => array(
                'groups' => array(
                    'main-form' => ['id', 'username', 'email', 'firstName', 'lastName', 'middleName', 'meta'],
                    'list' => function (User $user) {
                        $groups = array();
                        foreach ($user->getGroups() as $group) {
                            $groups[] = $group->getName();
                        }

                        return array(
                            'id' => $user->getId(),
                            'username' => $user->getUsername(),
                            'email' => $user->getEmail(),
                            'firstName' => $user->getFirstName(),
                            'lastName' => $user->getLastName(),
                            'middleName' => $user->getMiddleName(),
                            'state' => $user->getState(),
                            'groups' => $groups,
                            'meta' => $user->getMeta(),
                        );
                    },
                    'compact-list' => ['id', 'username', 'fullname'],
                    'delete-user' => ['username'],
                ),
                'profiles' => array(
                    'list',
                    'delete-user',
                    'main-form',
                    'compact-list',
                    'modera-backend-security-group-groupusers' => HydrationProfile::create(false)->useGroups(array('compact-list')),
                ),
            ),
            'map_data_on_create' => function (array $params, User $entity, DataMapperInterface $defaultMapper, ContainerInterface $container) use ($self) {
                $defaultMapper->mapData($params, $entity);

                if (isset($params['plainPassword']) && $params['plainPassword']) {
                    $plainPassword = $params['plainPassword'];
                } else {
                    $plainPassword = $self->generatePassword();
                }

                $self->setPassword($entity, $plainPassword);
                if (isset($params['sendPassword']) && $params['sendPassword'] != '') {
                    /* @var MailService $mailService */
                    $mailService = $container->get('modera_backend_security.service.mail_service');
                    $mailService->sendPassword($entity, $plainPassword);
                }
            },
            'map_data_on_update' => function (array $params, User $entity, DataMapperInterface $defaultMapper, ContainerInterface $container) use ($self) {
                $defaultMapper->mapData($params, $entity);

                /* @var LoggerInterface $activityMgr */
                $activityMgr = $container->get('modera_activity_logger.manager.activity_manager');
                /* @var TokenStorageInterface $ts */
                $ts = $container->get('security.token_storage');

                if (isset($params['plainPassword']) && $params['plainPassword']) {
                    $self->setPassword($entity, $params['plainPassword']);
                    if (isset($params['sendPassword']) && $params['sendPassword'] != '') {
                        /* @var MailService $mailService */
                        $mailService = $container->get('modera_backend_security.service.mail_service');
                        $mailService->sendPassword($entity, $params['plainPassword']);
                    }

                    $activityMsg = T::trans('Password has been changed for user "%user%".', array('%user%' => $entity->getUsername()));
                    $activityContext = array(
                        'type' => 'user.password_changed',
                        'author' => $ts->getToken()->getUser()->getId(),
                    );
                    $activityMgr->info($activityMsg, $activityContext);
                } else {
                    $activityMsg = T::trans('Profile data is changed for user "%user%".', array('%user%' => $entity->getUsername()));
                    $activityContext = array(
                        'type' => 'user.profile_updated',
                        'author' => $ts->getToken()->getUser()->getId(),
                    );
                    $activityMgr->info($activityMsg, $activityContext);
                }
            },
            'remove_entities_handler' => function ($entities, $params, $defaultHandler, ContainerInterface $container) {
                /* @var UserService $userService */
                $userService = $container->get('modera_security.service.user_service');

                $operationResult = new OperationResult();

                foreach ($entities as $entity) {
                    /* @var User $entity*/
                    $userService->remove($entity);

                    $operationResult->reportEntity(User::clazz(), $entity->getId(), OperationResult::TYPE_ENTITY_REMOVED);
                }

                return $operationResult;
            },
        );
    }

    /**
     * @Remote
     */
    public function generatePasswordAction(array $params)
    {
        $plainPassword = $this->generatePassword();

        return array(
            'success' => true,
            'result' => array(
                'plainPassword' => $plainPassword,
            ),
        );
    }

    /**
     * @param int $length
     *
     * @return string
     */
    private function generatePassword($length = 8)
    {
        $plainPassword = '';
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        for ($i = 0; $i < $length; ++$i) {
            $plainPassword .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $plainPassword;
    }

    /**
     * @param User $user
     * @param $plainPassword
     */
    private function setPassword(User $user, $plainPassword)
    {
        $factory = $this->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $password = $encoder->encodePassword($plainPassword, $user->getSalt());
        $user->setPassword($password);
        $user->eraseCredentials();
    }
}
