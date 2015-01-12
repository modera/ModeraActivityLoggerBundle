# ModeraDynamicallyConfigurableAppBundle

Bundle configuration properties that allow to dynamically configure how AppKernel is bootstrapped ( env, is-debug ).

## Installation

Add this dependency to your composer.json:

    "modera/dynamically-configurable-app-bundle": "dev-master"

Update your AppKernel class and add this:

    new Modera\DynamicallyConfigurableAppBundle\ModeraDynamicallyConfigurableAppBundle(),

## Licensing

This bundle is under the MIT license. See the complete license in the bundle:
Resources/meta/LICENSE