# ModeraFoundationBundle [![Build Status](https://travis-ci.org/modera/ModeraFoundationBundle.svg?branch=master)](https://travis-ci.org/modera/ModeraFoundationBundle)

Bundle ships some very basic utility classes

## Installation

Add this dependency to your composer.json:

    "modera/foundation-bundle": "dev-master"

Update your AppKernel class and add ModeraFoundationBundle declaration there:

    new Modera\FoundationBundle\ModeraFoundationBundle(),

## Documentation

### Functional testing

To streamline and simplify procedure of writing functional tests this bundles provides a super-type test case
that you can subclass when writing your own functional tests - `Modera\FoundationBundle\Testing\FunctionalTestCase`. For
more details please see docblock of the class itself.

## Licensing

This bundle is under the MIT license. See the complete license in the bundle:
Resources/meta/LICENSE

