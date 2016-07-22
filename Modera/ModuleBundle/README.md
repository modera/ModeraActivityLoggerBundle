# ModeraModuleBundle

[![Build Status](https://travis-ci.org/modera/ModeraModuleBundle.svg?branch=master)](https://travis-ci.org/modera/ModeraModuleBundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/modera/ModeraModuleBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/modera/ModeraModuleBundle/?branch=master)
[![StyleCI](https://styleci.io/repos/29133054/shield)](https://styleci.io/repos/29133054)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/56e241ed-05dc-4d33-a079-3d0a9ee6b98f/mini.png)](https://insight.sensiolabs.com/projects/56e241ed-05dc-4d33-a079-3d0a9ee6b98f)

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

    "modera/module-bundle": "~1.0"

Update your AppKernel class and add this:

    new Modera\ModuleBundle\ModeraModuleBundle(),

## Licensing

This bundle is under the MIT license. See the complete license in the bundle:
Resources/meta/LICENSE
