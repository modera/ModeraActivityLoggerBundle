<?php

namespace Modera\ServerCrudBundle\Tests\Fixtures\Bundle\Contributions;

use Modera\ServerCrudBundle\Tests\Fixtures\DummyInterceptor;
use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ControllerActionInterceptorsProvider implements ContributorInterface
{
    public $interceptor;

    public function __construct()
    {
        $this->interceptor = new DummyInterceptor();
    }

    public function getItems()
    {
        return array(
            $this->interceptor,
        );
    }
}
