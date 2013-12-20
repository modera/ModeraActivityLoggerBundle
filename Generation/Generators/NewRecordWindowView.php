<?php

namespace Modera\AdminGeneratorBundle\Generation\Generators;

use Modera\AdminGeneratorBundle\Generation\GenerationResult;
use Modera\AdminGeneratorBundle\Generation\ViewInterface;
use Modera\AdminGeneratorBundle\Generation\GeneratorsManager;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class NewRecordWindowView implements ViewInterface
{
    private $twig;
    private $generatorsManager;

    /**
     * @param \Twig_Environment $twig
     * @param GeneratorsManager $generatorsManager
     */
    public function __construct(\Twig_Environment $twig, GeneratorsManager $generatorsManager)
    {
        $this->twig = $twig;
        $this->generatorsManager = $generatorsManager;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return 'new-record-window';
    }

    /**
     * @return string
     */
    public function getActivationEventName()
    {
        return 'newrecord';
    }

    /**
     * @return string
     */
    public function createClassName(Section $section)
    {
        $config = $section->getConfig();

        return $config['root_namespace'] . '.runtime.NewWindow';
    }

    /**
     * @return string
     *
     * @return boolean
     */
    public function isResponsibleForClass($className, Section $section)
    {
        if ($this->createClassName($section) == $className) {
            return true;
        }

        $uiGenerator = $this->generatorsManager->getUiGeneratorByRole('new-record-window', $this);
        if ($uiGenerator && $uiGenerator->isResponsibleForClass($className, $section, $this)) {
            return true;
        }

        return false;
    }

    protected function prepareTemplateConfig(Section $section)
    {
        $viewConfig = $section->getViewConfig($this);

        $defaultViewParams = array(
            'class_name' => $this->createClassName($section),
            'ui_class' => $viewConfig['root_namespace'] . '.view.NewWindow'
        );

        return array_merge($defaultViewParams, $viewConfig);
    }

    /**
     * @return GenerationResult
     */
    public function generate($className, Section $section)
    {
        if ($this->createClassName($section) == $className) {
            return new GenerationResult(
                $this->twig->render('runtime/new-record-window-view.twig', $this->prepareTemplateConfig($section)),
                $this->createClassName($section)
            );
        }

        $uiGenerator = $this->generatorsManager->getUiGeneratorByRole('new-record-window', $this);
        if ($uiGenerator && $uiGenerator->isResponsibleForClass($className, $section, $this)) {
            return $uiGenerator->generate($className, $section, $this);
        }

        return false;
    }
}