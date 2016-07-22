# ModeraBackendConfigUtilsBundle

[![Build Status](https://travis-ci.org/modera/ModeraBackendConfigUtilsBundle.svg?branch=master)](https://travis-ci.org/modera/ModeraBackendConfigUtilsBundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/modera/ModeraBackendConfigUtilsBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/modera/ModeraBackendConfigUtilsBundle/?branch=master)
[![StyleCI](https://styleci.io/repos/29131777/shield)](https://styleci.io/repos/29131777)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/4a5625ea-b769-441a-a95b-10c38f405110/mini.png)](https://insight.sensiolabs.com/projects/4a5625ea-b769-441a-a95b-10c38f405110)

Bundle provides tools that simplify contributing your own configuration sections to "Backend/Tools/Settings" section.

## Installation

Add this dependency to your composer.json:

    "modera/backend-config-utils-bundle": "~1.0"

Update your AppKernel class and add this:

    new Modera\BackendConfigUtilsBundle\ModeraBackendConfigUtilsBundle(),

## Documentation

For a example how to contribute a settings page using tools provided by this bundle please take a look at
[SettingsSectionsProvider](https://github.com/modera/ModeraBackendGoogleAnalyticsConfigBundle/blob/master/Contributions/SettingsSectionsProvider.php)
from ModeraBackendGoogleAnalyticsConfigBundle, this is how it is going to look in UI:

![Settings page from ModeraBackendGoogleAnalyticsConfigBundle](Resources/screenshots/ModeraBackendConfigUtilsBundle.png)

For more details regarding available configuration properties for in-place editor fields available for configuration
grid take a look at [PropertiesGrid.js](Resources/public/js/view/PropertiesGrid.js).

## Licensing

This bundle is under the MIT license. See the complete license in the bundle:
Resources/meta/LICENSE
