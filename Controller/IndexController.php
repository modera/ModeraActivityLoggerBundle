<?php

namespace Modera\AdminGeneratorBundle\Controller;

use Modera\AdminGeneratorBundle\Generation\Section;
use Modera\AdminGeneratorBundle\Generation\GeneratorsManager;
use Modera\FoundationBundle\Controller\AbstractBaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Component\HttpFoundation\Response;
use Modera\JSRuntimeIntegrationBundle\Sections\SectionInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class IndexController extends AbstractBaseController
{
    /**
     * @Route("/crud/{className}.js", requirements={"className"=".+"})
     */
    public function indexAction($className)
    {
        /* @var ContributorInterface $sectionsProvider */
        $sectionsProvider = $this->get('mf.jsruntimeintegration.sections_provider');

        $generatorConfig = null;

        foreach ($sectionsProvider->getItems() as $section) {
            /* @var SectionInterface $section */

            $meta = $section->getMetadata();

            if (isset($meta[SectionInterface::META_NAMESPACE])) {
                $mappedNamespace = $meta[SectionInterface::META_NAMESPACE];

                if (substr($className, 0, strlen($mappedNamespace)) == $mappedNamespace && isset($meta['generator_config'])) {
                    $generatorConfig = $meta['generator_config'];
                }
            }
        }

        $twig = $this->get('modera_admin_generator.generation.twig');

        $section = new Section($generatorConfig, new GeneratorsManager($this->container), $twig);
        if ($section->isResponsibleForClass($className)) {
            $sourceCode = $section->generate($className)->getSourceCode();

            return new Response($sourceCode, 200, array('Content-Type' => 'application/javascript'));
        } else {
            return new Response("Unable to find a section which is responsible for generating class '$className'.", 404);
        }
    }
}