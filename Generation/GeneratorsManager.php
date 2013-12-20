<?php

namespace Modera\AdminGeneratorBundle\Generation;

use Doctrine\ORM\EntityManager;
use Modera\AdminGeneratorBundle\Generation\Generators\GridPanel;
use Modera\AdminGeneratorBundle\Generation\Generators\ListStore;
use Modera\AdminGeneratorBundle\Generation\Generators\ListView;
use Modera\AdminGeneratorBundle\Generation\Generators\NewRecordWindow;
use Modera\AdminGeneratorBundle\Generation\Generators\NewRecordWindowView;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class GeneratorsManager
{
    private $uiGenerators = array();
    private $viewGenerators = array();
    private $storeGenerators = array();

    private $container;

    /* @var \Twig_Environment */
    private $twig;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        /* @var \Twig_Environment $twig */
        $twig = $this->container->get('modera_admin_generator.generation.twig');
        /* @var EntityManager $em */
        $em = $container->get('doctrine.orm.entity_manager');

        $listView = new ListView($twig, $this);

        $this->viewGenerators[$listView->getId()] = $listView;

        $this->uiGenerators = array(
            'list' => new GridPanel($twig, $this, $em->getMetadataFactory()),
            'new-record-window' => new NewRecordWindow($twig, $this, $em->getMetadataFactory())
        );

        $this->storeGenerators = array(
            'list' => new ListStore($twig, $em->getMetadataFactory())
        );
    }

    public function getUiGeneratorByRole($role, ViewInterface $view)
    {
        if ('list' == $role && $view instanceof ListView) {
            return $this->uiGenerators['list'];
        } else if ('new-record-window' == $role && $view instanceof NewRecordWindowView) {
            return $this->uiGenerators['new-record-window'];
        }
    }

    public function getStoreGenerateByRole($role, $ui)
    {
        return $this->storeGenerators['list'];
    }

    /**
     * @param string $id
     *
     * @return string
     */
    public function findViewGenerator($id)
    {
        return isset($this->viewGenerators[$id]) ? $this->viewGenerators[$id] : false;
    }

    public function findUiGenerator($id)
    {

    }

    public function getStoreGenerator($id)
    {

    }
}