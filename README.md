# ModeraUpgradeBundle

[![Build Status](https://travis-ci.org/modera/ModeraUpgradeBundle.svg?branch=master)](https://travis-ci.org/modera/ModeraUpgradeBundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/modera/ModeraUpgradeBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/modera/ModeraUpgradeBundle/?branch=master)
[![StyleCI](https://styleci.io/repos/29133176/shield)](https://styleci.io/repos/29133176)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/e0f6dba2-92a4-4be3-a311-c5e2c1226caf/mini.png)](https://insight.sensiolabs.com/projects/e0f6dba2-92a4-4be3-a311-c5e2c1226caf)

Bundle simplifies upgrading procedure of Composer packages.

## Installation

Add the bundle to your project by running:

    composer require modera/upgrade-bundle

Update your AppKernel class and add this:

    new Modera\UpgradeBundle\ModeraUpgradeBundle(),

## Documentation

Run this command to get help how to use package upgrade command:

    app/console modera:upgrade --help

## Licensing

This bundle is under the MIT license. See the complete license in the bundle:
Resources/meta/LICENSE
