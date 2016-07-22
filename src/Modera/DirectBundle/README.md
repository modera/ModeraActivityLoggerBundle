# ModeraDirectBundle

[![StyleCI](https://styleci.io/repos/29132402/shield)](https://styleci.io/repos/29132402)

ModeraDirectBundle is an implementation of ExtDirect specification to Symfony2 framework.

## Installation

### Step 1: Update your vendors by running

``` bash
$ php composer.phar require modera/direct-bundle:dev-master
```

### Step 2: Enable the bundle

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Modera\DirectBundle\ModeraDirectBundle(),
    );
}
```

### Step 3: Add routing

``` yaml
// app/config/routing.yml

direct:
    resource: "@ModeraDirectBundle/Resources/config/routing.yml"
```

## How to use

### Add the ExtDirect API into your page

If you is using Twig engine, only add the follow line in your views page at the
script section:

``` html
<script type="text/javascript" src="{{ url('api') }}"></script>
```

Or if you are not using a template engine:

``` html
<script type="text/javascript" src="/api.js"></script>
```

### Expose your controller methods to ExtDirect Api

``` php
// .../Acme/DemoBundle/Controller/ExampleController.php

namespace Acme\DemoBundle\Controller;

use Modera\DirectBundle\Annotation\Form;
use Modera\DirectBundle\Annotation\Remote;

class ExampleController extends Controller
{
   /**
    * Single exposed method.
    *
    * @Remote    // this annotation expose the method to API
    *
    * @param  array $params
    * @return string
    */
    public function indexAction($params)
    {
        return 'Hello ' . $params['name'];
    }

    /**
     * An action to handle forms.
     *
     * @Remote   // this annotation expose the method to API
     * @Form     // this annotation expose the method to API with formHandler option
     *
     * @param array $params Form submitted values
     * @param array $files  Uploaded files like $_FILES
     */
    public function testFormAction($params, $files)
    {
        // your proccessing
        return true;
    }
}
```

### Call the exposed methods from JavaScript

``` js
// 'AcmeDemo' is the Bundle name without 'Bundle'
// 'Example' is the Controller name without 'Controller'
// 'index' is the method name without 'Action'
Actions.AcmeDemo_Example.index({ name: 'ExtDirect' }, function(r) {
   alert(r);
});
```

## Licensing

This bundle is under the MIT license. See the complete license in the bundle:
Resources/meta/LICENSE
