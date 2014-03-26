<?php

namespace Modera\BackendToolsActivityLogBundle\Controller;

use Modera\ServerCrudBundle\Hydration\DoctrineEntityHydrator;
use Modera\ServerCrudBundle\Hydration\HydrationService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Neton\DirectBundle\Annotation\Remote;

class DefaultController extends Controller
{
    /**
     * @return HydrationService
     */
    private function getHydrationService()
    {
        return $this->container->get('modera_server_crud.hydration.hydration_service');
    }

    private function getConfig()
    {
        return array(
            'groups' => array(
                'list' => DoctrineEntityHydrator::create(['meta']),
                'details' => ['id', 'meta']
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
        return array(

        );
    }

    /**
     * @Remote
     */
    public function listAction(array $params)
    {
        return [
            array(
                'id' => 1,
                'message' => 'foo',
                'createdAt' => 'x123kl',
                'author' => 'Vassily Pupkin',
                'type' => 'test.xf',
                'level' => 'critical'
            )
        ];
    }
}
