<?php

namespace Modera\AdminGeneratorBundle\Generation\Generators;

use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Modera\AdminGeneratorBundle\Generation\GenerationResult;
use Modera\AdminGeneratorBundle\Generation\ViewInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class ListStore
{
    private $twig;
    private $metadataFactory;

    /**
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig, ClassMetadataFactory $metadataFactory)
    {
        $this->twig = $twig;
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * @param Section $section
     * @param ViewInterface $view
     * @param $ui
     *
     * @return string
     */
    public function createClassName(Section $section, ViewInterface $view, $ui)
    {
        $sectionConfig = $section->getConfig();

        return $sectionConfig['root_namespace'] . '.store.List';
    }

    /**
     * @param string        $className
     * @param Section       $section
     * @param ViewInterface $view
     * @param ListView      $view
     *
     * @return bool
     */
    public function isResponsibleForClass($className, Section $section, ViewInterface $view, $ui)
    {
        return $this->createClassName($section, $view, $ui) == $className;
    }

    /**
     * @param Section $section
     * @param ViewInterface $view
     * @param $ui
     *
     * @return array
     */
    public function getPreparedConfig(Section $section, ViewInterface $view, GridPanel $ui)
    {
        $viewConfig = $section->getViewConfig($view);

        $preparedConfig = array(
            'class_name' => $this->createClassName($section, $view, $this),
            'direct_action_name' => 'Actions.ModeraServerCrud_Data.list',
            'fields' => array()
        );

        if (!isset($viewConfig['ui']['store']['fields'])) {
            $uiPreparedConfig = $ui->getPreparedConfig($section, $view);

            foreach ($uiPreparedConfig['columns'] as $column) {
                $preparedConfig['fields'][] = array(
                    'name' => $column['definition']['dataIndex'],
                    'type' => 'string'
                );
            }
        }

        if (isset($viewConfig['ui']['store'])) {
            return array_merge($preparedConfig, $viewConfig['ui']['store']);
        } else {
            return $preparedConfig;
        }
    }

    /**
     * @param string        $className
     * @param Section       $section
     * @param ViewInterface $view
     * @param $ui
     *
     * @return bool|GenerationResult
     */
    public function generate($className, Section $section, ViewInterface $view, $ui)
    {
        if ($this->createClassName($section, $view, $ui) == $className) {
            return new GenerationResult(
                $this->twig->render('store/list-store.twig', $this->getPreparedConfig($section, $view, $ui)),
                $this->createClassName($section, $view, $ui)
            );
        }

        return false;
    }
}