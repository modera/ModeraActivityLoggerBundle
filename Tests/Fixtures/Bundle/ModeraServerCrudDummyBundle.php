<?php

namespace Modera\ServerCrudBundle\Tests\Fixtures\Bundle;

use Modera\ServerCrudBundle\Tests\Fixtures\Bundle\DependencyInjection\ModeraServerCrudDummyExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ModeraServerCrudDummyBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->registerExtension(new ModeraServerCrudDummyExtension());
    }

} 