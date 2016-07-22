<?php

namespace Modera\FileRepositoryBundle\Tests\Fixtures\Bundle;

use Modera\TranslationsBundle\Tests\Fixtures\Bundle\DependencyInjection\ModeraTranslationsDummyExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ModeraDummyBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->setParameter('sys_temp_dir', sys_get_temp_dir());
    }
}
