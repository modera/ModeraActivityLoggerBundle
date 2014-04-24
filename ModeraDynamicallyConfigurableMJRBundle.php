<?php

namespace Modera\DynamicallyConfigurableMJRBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ModeraDynamicallyConfigurableMJRBundle extends Bundle
{
    const CONFIG_TITLE = 'site_name';
    const CONFIG_URL = 'url';
    const CONFIG_HOME_SECTION = 'home_section';
}
