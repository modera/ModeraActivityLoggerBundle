<?php

namespace Modera\JSRuntimeIntegrationBundle\Contributions;

use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class JsResourcesProvider implements ContributorInterface
{
    /**
     * @inheritDoc
     */
    public function getItems()
    {
        // http://cdnjs.com/libraries/moment.js/
        return array(
            '//cdnjs.cloudflare.com/ajax/libs/moment.js/2.4.0/moment-with-langs.min.js'
        );
    }
}