# ModeraRoutingBundle
[![Build Status](https://travis-ci.org/modera/ModeraRoutingBundle.svg?branch=master)](https://travis-ci.org/modera/ModeraRoutingBundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/modera/ModeraRoutingBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/modera/ModeraRoutingBundle/?branch=master)
[![StyleCI](https://styleci.io/repos/20248909/shield)](https://styleci.io/repos/20248909)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/f6b8c8d6-b528-4134-a8c7-e1d43c753fc2/mini.png)](https://insight.sensiolabs.com/projects/f6b8c8d6-b528-4134-a8c7-e1d43c753fc2)

This bundle makes it possible for bundles to dynamically include routing files so you don't need to manually register
them in root `app/config/routing.yml` file.

## Installation

Add this dependency to your composer.json:

    "modera/routing-bundle": "~1.0"

Update your AppKernel class and add these bundles there:

    new Sli\ExpanderBundle\SliExpanderBundle(),
    new Modera\RoutingBundle\ModeraRoutingBundle(),

At finally update your root routing.yml (`app/config/routing.yml`) file by adding this line there:

    _modera_routing:
        resource: "@ModeraRoutingBundle/Resources/config/routing.yml"

## Documentation

Internally `ModeraRoutingBundle` relies on `SliExpanderBundle` to leverage a consistent approach to creating extension
points. Shortly speaking, in order for a bundle to contribute routing resources it has to do two things:

 1. Create a contributor class which implements \Sli\ExpanderBundle\Ext\ContributorInterface
 2. Register it in a service container with tag `modera_routing.routing_resources_provider`.

This is how your contributor class may look like:

    namespace Modera\ExampleBundle\Contributions;

    use Sli\ExpanderBundle\Ext\ContributorInterface;

    class RoutingResourcesProvider implements ContributorInterface
    {
        public function getItems()
        {
            return array(
                '@ModeraExampleBundle/Resources/config/routing.yml'
            );
        }
    }

And here we have its service container definition:

    <service id="modera_example.contributions.routing_resources_provider"
             class="Modera\ExampleBundle\Contributions\RoutingResourcesProvider">

        <tag name="modera_routing.routing_resources_provider" />
    </service>

Since version v1.1 a simplified way of contributing new routing resources has been added (which
doesn't require adding intermediate files). Instead of having getItems() method return a path
to a routing file you can now return normalized file's content:


    public function getItems()
    {
        return array(
            array(
                'resource' => '@AcmeFooBundle/Controller/DefaultController.php',
                'type' => 'annotation',
            ),
        );
    }

## Licensing

This bundle is under the MIT license. See the complete license in the bundle:
Resources/meta/LICENSE
