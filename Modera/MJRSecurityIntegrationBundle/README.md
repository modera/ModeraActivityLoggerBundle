# ModeraMJRSecurityIntegrationBundle

[![Build Status](https://travis-ci.org/modera/ModeraMJRSecurityIntegrationBundle.svg?branch=master)](https://travis-ci.org/modera/ModeraMJRSecurityIntegrationBundle)
[![StyleCI](https://styleci.io/repos/29132608/shield)](https://styleci.io/repos/29132608)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/modera/ModeraMJRSecurityIntegrationBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/modera/ModeraMJRSecurityIntegrationBundle/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/e4171535-f597-4d2a-940e-dce1b4fc0581/mini.png)](https://insight.sensiolabs.com/projects/e4171535-f597-4d2a-940e-dce1b4fc0581)

Bundle provides integration layer which is necessary to make MJR to be security aware (authentication, authorization).

## Installation

Add this dependency to your composer.json:

    "modera/mjr-security-integration-bundle": "dev-master"

Update your AppKernel class and add this:

    new Modera\MJRSecurityIntegrationBundle\ModeraMJRSecurityIntegrationBundle(),

## Licensing

This bundle is under the MIT license. See the complete license in the bundle:
Resources/meta/LICENSE