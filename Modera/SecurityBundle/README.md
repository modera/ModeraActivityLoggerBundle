# ModeraSecurityBundle
[![Build Status](https://travis-ci.org/modera/ModeraSecurityBundle.svg?branch=master)](https://travis-ci.org/modera/ModeraSecurityBundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/modera/ModeraSecurityBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/modera/ModeraSecurityBundle/?branch=master)
[![StyleCI](https://styleci.io/repos/29133119/shield)](https://styleci.io/repos/29133119)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/6612e08b-1f76-47a9-ad29-af085f9a62ac/mini.png)](https://insight.sensiolabs.com/projects/6612e08b-1f76-47a9-ad29-af085f9a62ac)

Provides low level security integration layer for Symfony and a user-groups-permissions Doctrine ORM mapped domain model.

## Installation

Add this dependency to your composer.json:

    "modera/security-bundle": "~2.0"

Update your AppKernel class and add this:

    new Modera\SecurityBundle\ModeraSecurityBundle(),

## Licensing

This bundle is under the MIT license. See the complete license in the bundle:
Resources/meta/LICENSE