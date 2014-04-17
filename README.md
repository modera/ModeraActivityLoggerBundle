# ModeraUpgradeBundle

## Installation

### Step 1: update your vendors by running

    $ php composer.phar require modera/upgrade-bundle:dev-master

### Step2: Enable the bundle

    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Modera\UpgradeBundle\ModeraUpgradeBundle(),
        );
    }

### Step3: Update dependencies in "composer.json"

    $ php app/console modera:upgrade --dependencies

## License

This bundle is under the MIT license.
