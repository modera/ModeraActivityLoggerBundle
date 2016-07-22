<?php

$loaderFile = __DIR__.'/../vendor/autoload.php';

if (!is_file($loaderFile)) {
    throw new \LogicException('Could not find autoload.php in vendor/. Did you run "composer install --dev"?');
}

// for some reason without manually mapping Symfony namespace manually
// when running tests class 'Symfony\Component\HttpKernel\Bundle\Bundle' won't be found
$loader = require $loaderFile;
$loader->add('Symfony', __DIR__.'/../vendor/symfony/symfony/src');
