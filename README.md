# ModeraDynamicallyConfigurableMJRBundle

Given that `ModeraBackendToolsSettingsConfigBundle` is installed this bundle provides integration tools that make it possible
to configure dynamically through UIs in "Backend / Tools / Settings / General" the following aspects of backend:

 * Site name
 * Site URL
 * Home section

## Installation

Add this dependency to your composer.json:

    "modera/dynamically-configurable-mjr-bundle": "dev-master"

Update your AppKernel class and add this:

    new Modera\DynamicallyConfigurableMJRBundle\ModeraDynamicallyConfigurableMJRBundle(),

## Licensing

This bundle is under the MIT license. See the complete license in the bundle:
Resources/meta/LICENSE