<?php

namespace Modera\AdminGeneratorBundle\Generation;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class Section
{
    private $config;
    private $viewsManager;
    private $twig;
    /* @var ViewInterface[] */
    private $views;

    /**
     * @param array        $config
     * @param GeneratorsManager $viewsManager
     */
    public function __construct(array $config, GeneratorsManager $viewsManager, \Twig_Environment $twig)
    {
        $this->config = $config;

        $this->viewsManager = $viewsManager;
        $this->twig = $twig;

        $this->views = array();
        foreach ($config['views'] as $key => $value) {
            $id = is_array($value) ? $key : $value;

            $view = $this->viewsManager->findViewGenerator($id);
            if (!$view) {
                throw new \RuntimeException("Unable to find a View with ID '$id'.");
            }

            $this->views[$id] = $view;
        }
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function createClassName()
    {
        return $this->config['root_namespace'] . '.runtime.Section';
    }

    /**
     * @param ViewInterface $view
     *
     * @return array
     */
    public function getViewConfig(ViewInterface $view)
    {
        $sectionConfig = $this->getConfig();

        $viewConfig = array();
        foreach ($sectionConfig['views'] as $key => $value) {
            if (is_array($value) && $view->getId() == $key) {
                $viewConfig = $value;
            }
        }

        $defaultConfig = array(
            'root_namespace' => $sectionConfig['root_namespace'],
            'entity' => $sectionConfig['entity'],
            'is_home_view' => $view->getId() == $sectionConfig['home_view']
        );

        return array_merge($viewConfig, $defaultConfig);
    }

    private function getResponsibleView($className)
    {
        foreach ($this->views as $view) {
            if ($view->isResponsibleForClass($className, $this)) {

                return $view;
            }
        }

        return false;
    }

    /**
     * @param string $className
     * @return boolean
     */
    public function isResponsibleForClass($className)
    {
        if ($this->createClassName() === $className) {
            return true;
        }

        if ($this->getResponsibleView($className, $this)) {
            return true;
        }

        return false;
    }

    private function generateSectionClass()
    {
        $params = array(
            'class_name' => $this->createClassName(),
            'runtime_views' => array()
        );

        foreach ($this->views as $view) {
            $params['runtime_views'][] = array(
                'activation_event_name' => $view->getActivationEventName(),
                'class_name' => $view->createClassName($this)
            );
        }

        return new GenerationResult($this->twig->render('runtime/section.twig', $params), $this->createClassName());
    }

    /**
     * @param string $className
     * @return boolean
     */
    public function generate($className)
    {
        if ($this->createClassName() == $className) {
            return $this->generateSectionClass();
        }

        $view = $this->getResponsibleView($className);
        if (!$view) {
            throw new \RuntimeException("Unable to find a view which is responsible for '$className'.");
        }

        return $view->generate($className, $this);
    }
}