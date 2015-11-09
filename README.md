# ModeraModuleBundle [![StyleCI](https://styleci.io/repos/29133054/shield)](https://styleci.io/repos/29133054)

Makes it possible to dynamically install and inject new bundles to your AppKernel class when a new bundle
is installed by composer. To make your bundle susceptible to automatic kernel installation please add similar lines
to your `composer.json` file:

    "extra": {
        "modera-module": {
            "register-bundle": "MyCompany\\HelloWorldBundle\\MyCompanyHelloWorldBundle"
        }
    }

## Installation

Add this dependency to your composer.json:

    "modera/module-bundle": "dev-master"

Update your AppKernel class and add this:

    new Modera\ModuleBundle\ModeraModuleBundle(),

## Licensing

This bundle is under the MIT license. See the complete license in the bundle:
Resources/meta/LICENSE