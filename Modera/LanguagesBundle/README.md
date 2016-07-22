# ModeraLanguagesBundle [![Build Status](https://travis-ci.org/modera/ModeraLanguagesBundle.svg?branch=master)](https://travis-ci.org/modera/ModeraLanguagesBundle)

Bundle provide set of basic utilities that allow you to define your site languages configuration in a config file and
then have it synchronized with database so you can establish database relations between languages and some other
entities that your project has.

## Installation

### Step 1: update your vendors by running

    $ php composer.phar require modera/languages-bundle:dev-master

### Step2: Enable the bundle

    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Modera\LanguagesBundle\ModeraLanguagesBundle(),
        );
    }

### Step3: Add config

This is a sample configuration:

    // app/config/config.yml

    modera_languages:
        - { locale: en }
        - { locale: ru, is_enabled: false }
        - { locale: et }

Later if you remove a language from `modera_languages` and run `modera:languages:config-sync` command then a database
record which corresponded to a deleted from a config file language will be marked as `isEnabled = false`.

### Step4: Create schema

    $ php app/console doctrine:schema:update --force

### Step5: Synchronize languages config with database.

    $ php app/console modera:languages:config-sync

## Licensing

This bundle is under the MIT license. See the complete license in the bundle:
Resources/meta/LICENSE
