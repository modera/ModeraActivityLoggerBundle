# ModeraLanguagesBundle

## Installation

### Step 1: update your vendors by running

``` bash
$ php composer.phar require modera/languages-bundle:dev-master
```

### Step2: Enable the bundle

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...

        new Modera\LanguagesBundle\ModeraLanguagesBundle(),
    );
}
```

### Step3: Add config

``` yaml
// app/config/config.yml

modera_languages:
    - { locale: en }
    - { locale: ru, is_enabled: false }
    - { locale: et }
```

### Step4: Create schema

``` bash
$ php app/console doctrine:schema:update --force
```

### Step5: Synchronize languages config with database.

``` bash
$ php app/console modera:languages:config-sync
```

## License

This bundle is under the MIT license. See the complete license in the bundle:

```
LICENSE
```
