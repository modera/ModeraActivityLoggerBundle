<?php

namespace Modera\AdminGeneratorBundle\Generation\Generators;

use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Modera\AdminGeneratorBundle\Generation\GenerationResult;
use Modera\AdminGeneratorBundle\Generation\GeneratorsManager;
use Modera\AdminGeneratorBundle\Generation\Section;
use Modera\AdminGeneratorBundle\Generation\ViewInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class GridPanel
{
    private $twig;
    private $generatorsManager;
    private $metadataFactory;

    /**
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig, GeneratorsManager $generatorsManager, ClassMetadataFactory $metadataFactory)
    {
        $this->twig = $twig;
        $this->generatorsManager = $generatorsManager;
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * @param Section       $section
     * @param ViewInterface $view
     *
     * @return string
     */
    public function createClassName(Section $section, ViewInterface $view)
    {
        $config = $section->getConfig();

        return $config['root_namespace'] . '.' . 'view.List';
    }

    /**
     * @param string        $className
     * @param Section       $section
     * @param ViewInterface $view
     *
     * @return bool
     */
    public function isResponsibleForClass($className, Section $section, ViewInterface $view)
    {
        if ($this->createClassName($section, $view) == $className) {
            return true;
        }

        $store = $this->generatorsManager->getStoreGenerateByRole('list', $this);
        if ($store && $store->isResponsibleForClass($className, $section, $view, $this)) {
            return true;
        }

        return false;
    }

    /**
     * @param Section       $section
     * @param ViewInterface $view
     *
     * @return array
     */
    public function getPreparedConfig(Section $section, ViewInterface $view)
    {
        $sectionConfig = $section->getConfig();

        $preparedConfig = array(
            'class_name' => $this->createClassName($section, $view),
            'store_class_name' => $sectionConfig['root_namespace'] . '.store.List',
            'columns' => array(),
            'skipped_columns' => array()
        );

        $viewConfig = $section->getViewConfig($view);

        if (!isset($viewConfig['ui']['columns'])) {
            /* @var ClassMetadataInfo $meta */
            $meta = $this->metadataFactory->getMetadataFor($sectionConfig['entity']);
            if (!$meta) {
                throw new \RuntimeException("Unable to find metadata for entity '{$sectionConfig['entity']}'.");
            }

            foreach ($meta->getFieldNames() as $fieldName) {
                $preparedConfig['columns'][] = array(
                    'localization_token' => $fieldName . 'ColumnText',
                    'text' => ucfirst($fieldName),
                    'definition' => array(
                        'dataIndex' => $fieldName
                    )
                );
            }
        }

        if (isset($viewConfig['ui'])) {
            return array_merge($preparedConfig, $viewConfig['ui']);
        } else {
            return $preparedConfig;
        }
    }

    /**
     * @param string        $className
     * @param Section       $section
     * @param ViewInterface $view
     *
     * @return GenerationResult
     */
    public function generate($className, Section $section, ViewInterface $view)
    {
        if ($this->createClassName($section, $view) == $className) {
            return new GenerationResult(
                $this->twig->render('view/grid-panel.twig', $this->getPreparedConfig($section, $view)),
                $this->createClassName($section, $view)
            );
        }

        $store = $this->generatorsManager->getStoreGenerateByRole('list', $view, $this);
        if ($store && $store->isResponsibleForClass($className, $section, $view, $this)) {
            return $store->generate($className, $section, $view, $this);
        }

        return false;
    }
}