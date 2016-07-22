# ModeraServerCrudBundle
[![Build Status](https://travis-ci.org/modera/ModeraServerCrudBundle.svg?branch=master)](https://travis-ci.org/modera/ModeraServerCrudBundle)
[![StyleCI](https://styleci.io/repos/17936357/shield)](https://styleci.io/repos/17936357)

The bundle provides a set of tools that simplifies building applications which need to operate with data coming
from client-side. These operations are supported:

 * Creating new records
 * Validating data ( both Symfony validation and domain validation )
 * Querying data - single record, batch
 * Removing record(s)
 * Getting default values that can be used on client-side as a template for a new record

What this bundle does:

 * Provides a super-type controller that you can inherit from to harness power of all aforementioned operations
 * Integrates a powerful querying language where you define queries using JSON - now you can safely build queries
   on client-side
 * Hydration package - this component provides a nice way of converting your entities to data-structure that can
   be understood by client-side logic
 * Provides a simple yet powerful client-server communication protocol
 * Simplifies functional testing of your controller

## Installation

Add this dependency to your composer.json:

    "modera/server-crud-bundle": "dev-master"

Update your AppKernel class and add ModeraFoundationBundle declaration there:

    new Modera\ServerCrudBundle\ModeraServerCrudBundle(),

## Documentation

For detailed documentation describing how to use this bundle and its components please read  `Resources/doc/index.md`.

## Licensing

This bundle is under the MIT license. See the complete license in the bundle:
Resources/meta/LICENSE

