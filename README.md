ModeraRoutingBundle
==============

## Configuration

app/config/routing.yml

```

_modera_routing:
    resource: "@ModeraRoutingBundle/Resources/config/routing.yml"

```

## Example

Modera\ExampleBundle\Contributions\RoutingResourcesProvider.php

```

<?php

namespace Modera\ExampleBundle\Contributions;

use Sli\ExpanderBundle\Ext\ContributorInterface;

class RoutingResourcesProvider implements ContributorInterface
{
    /**
     * @inheritDoc
     */
    public function getItems()
    {
        return array(
            '@ModeraExampleBundle/Resources/config/routing.yml'
        );
    }
}

```

Modera\ExampleBundle\Resurces\config\services.xml

```

<service>

...

    <service id="modera_example.contributions.routing_resources_provider"
         class="Modera\ExampleBundle\Contributions\RoutingResourcesProvider">

    <tag name="modera_routing.routing_resources_provider" />

...

</service>


```