<?php

namespace Modera\TranslationsBundle\Tests\Fixtures\Bundle;

use Modera\TranslationsBundle\Tests\Fixtures\Bundle\DependencyInjection\ModeraTranslationsDummyExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ModeraTranslationsDummyBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->registerExtension(new ModeraTranslationsDummyExtension());
    }
}
