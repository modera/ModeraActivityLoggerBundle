# ModeraDynamicallyConfigurableMJRBundle

[![Build Status](https://travis-ci.org/modera/ModeraDynamicallyConfigurableMJRBundle.svg?branch=master)](https://travis-ci.org/modera/ModeraDynamicallyConfigurableMJRBundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/modera/ModeraDynamicallyConfigurableMJRBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/modera/ModeraDynamicallyConfigurableMJRBundle/?branch=master)
[![StyleCI](https://styleci.io/repos/29132444/shield)](https://styleci.io/repos/29132444)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/1152f2c6-ed58-448f-8e69-d1fd03eaba4e/mini.png)](https://insight.sensiolabs.com/projects/1152f2c6-ed58-448f-8e69-d1fd03eaba4e)

Given that `ModeraBackendToolsSettingsConfigBundle` is installed this bundle provides integration tools that make it possible
to configure dynamically through UIs in "Backend / Tools / Settings / General" the following aspects of backend:

 * Site name
 * Site URL
 * Home section

## Installation

Add this dependency to your composer.json:

    "modera/dynamically-configurable-mjr-bundle": "~1.0"

Update your AppKernel class and add this:

    new Modera\DynamicallyConfigurableMJRBundle\ModeraDynamicallyConfigurableMJRBundle(),

## Licensing

This bundle is under the MIT license. See the complete license in the bundle:
Resources/meta/LICENSE