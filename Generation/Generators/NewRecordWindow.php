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
class NewRecordWindow
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

        return $config['root_namespace'] . '.' . 'view.NewWindow';
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
        return $this->createClassName($section, $view) == $className;
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
            'fields' => array()
        );

        $viewConfig = $section->getViewConfig($view);

        if (!isset($viewConfig['ui']['fields'])) {
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
                        'name' => $fieldName
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
                $this->twig->render('view/new-window.twig', $this->getPreparedConfig($section, $view)),
                $this->createClassName($section, $view)
            );
        }

        return false;
    }
}