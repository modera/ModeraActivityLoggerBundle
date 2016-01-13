# ModeraConfigBundle

[![Build Status](https://travis-ci.org/modera/ModeraConfigBundle.svg?branch=master)](https://travis-ci.org/modera/ModeraConfigBundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/modera/ModeraConfigBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/modera/ModeraConfigBundle/?branch=master)
[![StyleCI](https://styleci.io/repos/29132382/shield)](https://styleci.io/repos/29132382)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/66e48bb9-6ee7-4a6e-b698-ff42231c6d96/mini.png)](https://insight.sensiolabs.com/projects/66e48bb9-6ee7-4a6e-b698-ff42231c6d96)

Bundles provides tools that allow to you to dynamically store and fetch your configuration properties in a flexible way.
You can store any type of configuration property - your configuration property can store both simple values (like string,
integers, arrays) or complex ones - like objects or references to entities, this is achieved by using so called
"Handlers" (implementations of `\Modera\ConfigBundle\Config\HandlerInterface`).

## Installation

Add this dependency to your composer.json:

    "modera/config-bundle": "~1.0"

Update your AppKernel class and add this:

    new Modera\ConfigBundle\ModeraConfigBundle(),

## Publishing configuration properties

Before you can use your configuration properties you need to publish them. Publishing process consists of several steps:

1. Create a provider class.
2. Register your provides class in service container with "modera_config.config_entries_provider" tag.
3. Use `modera:config:install-config-entries` command to publish exposed configuration entries.

This is how a simple provider class could look like:

    namespace MyCompany\SiteBundle\Contributions;

    use Modera\ConfigBundle\Config\ConfigurationEntryDefinition as CED;
    use Sli\ExpanderBundle\Ext\ContributorInterface;

    class ConfigEntriesProvider implements ContributorInterface
    {
        private $em;

        public function __construct(EntityManager $em)
        {
            $this->em = $em;
        }

        /**
         * {@inheritdoc}
         */
        public function getItems()
        {
            $serverConfig = array(
                'id' => 'modera_config.entity_repository_handler'
            );

            $admin = $this->em->find('MyCompany\SecurityBundle\Entity\User', 1);

            return array(
                new CED('admin_user', 'Site administrator', $admin)
            );
        }
    }

Once you have a class you need to register it in a service container:

    <service id="my_company_site.contributions.config_entries_provider"
             class="MyCompany\SiteBundle\Contributions\ConfigEntriesProvider">

        <tag name="modera_config.config_entries_provider" />
    </service>

Now we can use `modera:config:install-config-entries` to publish our configuration property.

## Fetching configuration properties

In order to fetch a configuration property in your application code you need to use
`modera_config.configuration_entries_manager` service.

    /* @var \Modera\ConfigBundle\Config\ConfigurationEntriesManagerInterface $service */
    $service = $container->get('modera_config.configuration_entries_manager');

    /* @var \Modera\ConfigBundle\Config\ConfigurationEntryInterface $entry */
    $entry = $service->findOneByNameOrDie('admin_user');

    // will yield "MyCompany\SecurityBundle\Entity\User"
    echo get_class($property->getValue());

## Twig integration

The bundle also provides integration with Twig that allow you to fetch configuration properties' values from your
template. For this you will want to use `modera_config_value` function:

    {{ modera_config_value("my_property_name") }}

This will print value for "my_property_name" configuration property. By default if no given configuration property
is found then exception is thrown but you can change this behaviour by passing FALSE as second argument to the function
and in this case NULL be returned instead of throwing an exception.

## Handlers

By default the bundle is capable of storing these types of values:

* string
* text
* float
* array
* boolean
* references to entities

If you need to store some more complex values then you need to implement `\Modera\ConfigBundle\Config\HandlerInterface`
interface. Please see already shipped implementations (`\Modera\ConfigBundle\Config\EntityRepositoryHandler`,
for example) to see how you can create your own handlers.

## Hints

In your application code when working with components from ModeraConfigBundle you should rely on interfaces instead of
implementations, that is, when you use `modera_config.configuration_entries_manager` rely on
\Modera\ConfigBundle\Config\ConfigurationEntriesManagerInterface and when working with configuration entries rely on
\Modera\ConfigBundle\Config\ConfigurationEntryInterface this way you will make your code portable. By default the bundle
uses Doctrine ORM to store values for configuration entries but later some more storage mechanism may be added and if you
rely on interfaces then you won't need to update your code to leverage new possible storage engines.

## Licensing

This bundle is under the MIT license. See the complete license in the bundle:
Resources/meta/LICENSE
