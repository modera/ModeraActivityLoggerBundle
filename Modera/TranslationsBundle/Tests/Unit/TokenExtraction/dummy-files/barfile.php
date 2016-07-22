<?php

use Modera\TranslationsBundle\Foo\T;
use Modera\TranslationsBundle\HelperX\T;

T::trans('This must not be parsed because of wrong namespace');