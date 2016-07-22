<?php

namespace Modera\BackendLanguagesBundle\Contributions;

use Doctrine\ORM\EntityManager;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Modera\SecurityBundle\Entity\User;
use Modera\BackendLanguagesBundle\Entity\UserSettings;
use Modera\MjrIntegrationBundle\DependencyInjection\ModeraMjrIntegrationExtension;

/**
 * Provides service definitions for client-side dependency injection container.
 *
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ClientDiServiceDefinitionsProvider implements ContributorInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        /* @var TokenStorageInterface $tokenStorage */
        $tokenStorage = $this->container->get('security.token_storage');
        /* @var RequestStack */
        $requestStack = $this->container->get('request_stack');
        /* @var Request $request */
        $request = $requestStack->getCurrentRequest();

        if (null === $request) {
            return array();
        }

        /* @var Router $router */
        $router = $this->container->get('router');
        $locale = $request->getLocale();
        $runtimeConfig = $this->container->getParameter(ModeraMjrIntegrationExtension::CONFIG_KEY);

        $token = $tokenStorage->getToken();
        if ($token->isAuthenticated() && $token->getUser() instanceof User) {
            /* @var EntityManager $em */
            $em = $this->container->get('doctrine.orm.entity_manager');
            /* @var UserSettings $settings */
            $settings = $em->getRepository(UserSettings::clazz())->findOneBy(array('user' => $token->getUser()->getId()));
            if ($settings && $settings->getLanguage() && $settings->getLanguage()->getEnabled()) {
                $locale = $settings->getLanguage()->getLocale();
                $session = $request->getSession();
                $session->set('_backend_locale', $locale);
            }
        }

        return array(
            'extjs_localization_runtime_plugin' => array(
                'className' => 'Modera.backend.languages.runtime.ExtJsLocalizationPlugin',
                'tags' => ['runtime_plugin'],
                'args' => array(
                    array(
                        'urls' => array(
                            $runtimeConfig['extjs_path'].'/locale/ext-lang-'.$locale.'.js',
                            $router->generate('modera_backend_languages_extjs_l10n', array('locale' => $locale)),
                        ),
                    ),
                ),
            ),
            'modera_backend_languages.user_settings_window_contributor' => array(
                'className' => 'Modera.backend.languages.runtime.UserSettingsWindowContributor',
                'args' => ['@application'],
                'tags' => ['shared_activities_provider'],
            ),
        );
    }
}
