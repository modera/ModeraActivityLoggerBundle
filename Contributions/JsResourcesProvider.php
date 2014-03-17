<?php

namespace Modera\SecurityAwareJSRuntimeBundle\Contributions;

use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class JsResourcesProvider implements ContributorInterface
{
    public function getItems()
    {
        return array(
            // \Modera\SecurityAwareJSRuntimeBundle\Controller\IndexController::applicationAction route
        );
    }
} 