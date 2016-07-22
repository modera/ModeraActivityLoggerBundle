<?php

namespace Modera\BackendToolsBundle\Controller;

use Modera\BackendToolsBundle\Section\Section;
use Modera\FoundationBundle\Controller\AbstractBaseController;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Modera\DirectBundle\Annotation\Remote;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class DefaultController extends AbstractBaseController
{
    /**
     * @Remote
     *
     * @param array $params
     *
     * @return array
     */
    public function getSectionsAction(array $params)
    {
        /* @var ContributorInterface $sectionsProvider */
        $sectionsProvider = $this->get('modera_backend_tools.sections_provider');

        $result = array();
        foreach ($sectionsProvider->getItems() as $section) {
            /* @var Section $section */
            $result[] = array(
                'name' => $section->getName(),
                'glyph' => $section->getGlyph(),
                'iconSrc' => $section->getIconSrc(),
                'iconCls' => $section->getIconClass(),
                'description' => $section->getDescription(),
                'section' => $section->getSection(),
                'activationParams' => $section->getSectionActivationParams()
            );
        }

        return $result;
    }
}