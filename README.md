# ModeraActivityLoggerBundle

Bundle provides facilities that let you to log different domain events that occur during your application logic execution,
later you are able to query those logged events ( they are called Activities in scope of this bundle ). The point
here is that later those activities can be reviewed by ordinary users to see what has been happening in the system.

Unless you need to query activities in your application logic please rely on a generic Psr's LoggerInterface interface
to log your activities.

## Installation

### Step 1: Download the Bundle

``` bash
composer require modera/activity-logger-bundle:4.x-dev
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation.

### Step 2: Enable the Bundle

This bundle should be automatically enabled by [Flex](https://symfony.com/doc/current/setup/flex.html).
In case you don't use Flex, you'll need to manually enable the bundle by
adding the following line in the `config/bundles.php` file of your project:

``` php
<?php
// config/bundles.php

return [
    // ...
    Modera\ActivityLoggerBundle\ModeraActivityLoggerBundle::class => ['all' => true],
];
```

## Documentation

To log your activities you will be using an implementation of standard `Psr\Log\LoggerInterface` interface which
means that your application won't directly depend on this bundle but rather will rely on a generic interface that later
you can switch ( say that you decided to use some default Monolog log handler ) if needed.

Bundle declares two additional interfaces - `Modera\ActivityLoggerBundle\Manager\ActivityManagerInterface` and
`Modera\ActivityLoggerBundle\Model\ActivityInterface`. The former extends Psr's LoggerInterface and adds one method -
"query", this method can be used to query activities. Activities returned by this method are implementations of
ActivityInterface. By default the bundle provides one implementation of ActivityManagerInterface which stores activities
using Doctrine ORM's EntityManager - `Modera\ActivityLoggerBundle\Manager\DoctrineOrmActivityManager`.

## Licensing

This bundle is under the MIT license. See the complete license in the bundle:
Resources/meta/LICENSE
