# ModeraTranslationsBundle

## Installation

### Step 1: update your vendors by running

``` bash
$ php composer.phar require modera/translations-bundle:dev-master
```

### Step2: Enable the bundle

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...

        new Modera\TranslationsBundle\ModeraTranslationsBundle(),
    );
}
```

### Step3: Create schema

``` bash
$ php app/console doctrine:schema:update --force
```

### Step4: Import translations

``` bash
$ php app/console modera:translations:import
```

### Step5: Compile translations

``` bash
$ php app/console modera:translations:compile
```

## Example of Usage

``` xml
<!--
// Acme/DemoBundle/Resources/config/services.xml
-->

<service parent="modera_translations.handling.template_translation_handler">

    <argument>AcmeDemoBundle</argument>

    <tag name="modera_translations.translation_handler" />
</service>

<service parent="modera_translations.handling.php_classes_translation_handler">

    <argument>AcmeDemoBundle</argument>

    <tag name="modera_translations.translation_handler" />
</service>

```

## License

This bundle is under the MIT license. See the complete license in the bundle:

```
LICENSE
```