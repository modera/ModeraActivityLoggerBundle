<?php

namespace Modera\BackendTranslationsToolBundle\Contributions;

use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Modera\JSRuntimeIntegrationBundle\DependencyInjection\ModeraJSRuntimeIntegrationExtension;

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
     * {@inheritDoc}
     */
    public function getItems()
    {
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
        $runtimeConfig = $this->container->getParameter(ModeraJSRuntimeIntegrationExtension::CONFIG_KEY);

        return array(
            'extjs_localization_runtime_plugin' => array(
                'className' => 'Modera.backend.translationstool.runtime.ExtJsLocalizationPlugin',
                'tags'      => ['runtime_plugin'],
                'args'      => array(
                    array(
                        'urls' => array(
                            $runtimeConfig['extjs_path'] . '/locale/ext-lang-' . $locale . '.js',
                            $router->generate('modera_backend_translations_tool_extjs_l10n', array('locale' => $locale)),
                        ),
                    )
                ),
            )
        );
    }
}