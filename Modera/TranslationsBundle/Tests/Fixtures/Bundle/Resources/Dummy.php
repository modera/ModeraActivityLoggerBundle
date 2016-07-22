<?php

namespace Modera\TranslationsBundle\Tests\Fixtures\Bundle\Resources;

use Modera\FoundationBundle\Translation\T;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class Dummy
{
    public function test()
    {
        return T::trans('Test token');
    }
}
