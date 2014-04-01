<?php

namespace Modera\BackendTranslationsToolBundle\Controller;

use Sli\ExtJsLocalizationBundle\Controller\IndexController as Controller;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class IndexController extends Controller
{
    protected function getTemplate()
    {
        return 'ModeraBackendTranslationsToolBundle:Index:compile.html.twig';
    }
}
