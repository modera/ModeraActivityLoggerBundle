<?php

namespace Modera\JSRuntimeIntegrationBundle\Contributions;

use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class JsResourcesProvider implements ContributorInterface
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @inheritDoc
     */
    public function getItems()
    {
        // http://cdnjs.com/libraries/moment.js/
        return array(
            '//cdnjs.cloudflare.com/ajax/libs/moment.js/2.4.0/moment-with-langs.min.js',
            $this->router->generate('mf_font_awesome'),
        );
    }
}