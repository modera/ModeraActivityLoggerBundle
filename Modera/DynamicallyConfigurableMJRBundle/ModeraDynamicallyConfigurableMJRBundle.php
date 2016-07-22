<?php

namespace Modera\DynamicallyConfigurableMJRBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ModeraDynamicallyConfigurableMJRBundle extends Bundle
{
    const CONFIG_TITLE = 'site_name';
    const CONFIG_URL = 'url';
    const CONFIG_HOME_SECTION = 'home_section';
}
