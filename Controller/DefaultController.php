<?php

namespace Modera\BackendToolsActivityLogBundle\Controller;

use Modera\ActivityLoggerBundle\Manager\ActivityManagerInterface;
use Modera\ActivityLoggerBundle\Model\ActivityInterface;
use Modera\BackendToolsActivityLogBundle\AuthorResolving\ActivityAuthorResolver;
use Modera\ServerCrudBundle\Hydration\DoctrineEntityHydrator;
use Modera\ServerCrudBundle\Hydration\HydrationService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Neton\DirectBundle\Annotation\Remote;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DefaultController extends Controller
{
    /**
     * @return HydrationService
     */
    private function getHydrationService()
    {
        return $this->container->get('modera_server_crud.hydration.hydration_service');
    }

    /**
     * @return ActivityManagerInterface
     */
    private function getActivityManager()
    {
        return $this->container->get('modera_activity_logger.manager.doctrine_orm_activity_manager');
    }

    /**
     * @return ActivityAuthorResolver
     */
    private function getActivityAuthorResolver()
    {
        return $this->container->get('modera_backend_tools_activity_log.activity_author_resolver');
    }

    private function getConfig()
    {
        $authorResolver = $this->getActivityAuthorResolver();

        return array(
            'groups' => array(
                'list' => function(ActivityInterface $activity, $container) use($authorResolver) {
                    $hydrator = DoctrineEntityHydrator::create(['meta', 'createdAt', 'author']);

                    return array_merge($hydrator($activity, $container), array(
                        'createdAt' => $activity->getCreatedAt()->format(\DateTime::RFC1123),
                        'author' => json_encode($authorResolver->resolve($activity))
                    ));
                },
                'details' => function(ActivityInterface $activity, ContainerInterface $container) use($authorResolver) {
                    $hydrator = DoctrineEntityHydrator::create();

                    return array_merge($hydrator($activity, $container), array(
                        'createdAt' => $activity->getCreatedAt()->format(\DateTime::RFC1123),
                        'author' => $authorResolver->resolve($activity)
                    ));
                }
            ),
            'profiles' => array(
                'list', 'details'
            )
        );
    }

    /**
     * @Remote
     */
    public function getAction(array $params)
    {
        $result = $this->getActivityManager()->query($params);

        if (count($result['items']) == 1) {
            return array(
                'result' => $this->getHydrationService()->hydrate($result['items'][0], $this->getConfig(), 'details'),
                'success' => true
            );
        } else {
            return array(
                'success' => false,
                'message' => 'Unable to find activity by given query'
            );
        }
    }

    /**
     * @Remote
     */
    public function listAction(array $params)
    {
        $result = $this->getActivityManager()->query($params);

        $response = array(
            'items' => []
        );

        foreach ($result['items'] as $activity) {
            /* @var ActivityInterface $activity */

            $response['items'][] = $this->getHydrationService()->hydrate($activity, $this->getConfig(), 'list');
        }

        return array_merge($result, $response, array(
            'success' => true
        ));
    }
}
