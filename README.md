# ModeraBackendToolsSettingsBundle

Provides a unified way of  exposing sections that would allow to configure your modules. This bundle contributes
a section to "Backend / Tools" called "Settings".

See `Modera\BackendToolsSettingsBundle\ModeraBackendToolsSettingsBundle` for a list of exposed extension points.

## Installation

Add this dependency to your composer.json:

    "modera/backend-tools-settings-bundle": "dev-master"

Update your AppKernel class and add this:

    new Modera\BackendToolsSettingsBundle\ModeraBackendToolsSettingsBundle(),

## How to contribute your own settings section

In order to just contribute a section ( an activity ) to Settings section you need to create a provider class
which would return instances of `Modera\BackendToolsSettingsBundle\Section\SectionInterface`. This is an example
how to contributor class could look like:

    namespace MyCompany\BlogBundle\Contributions;

    use Modera\BackendToolsSettingsBundle\Section\StandardSection;
    use Sli\ExpanderBundle\Ext\ContributorInterface;

    class SettingsSectionsProvider implements ContributorInterface
    {
        /**
         * @inheritDoc
         */
        public function getItems()
        {
            return array(
                new StandardSection(
                    'blog', 'Blog', 'Modera.backend.configutils.runtime.SettingsListActivity', 'gear', array('category' => 'blog')
                )
            );
        }
    }

Once you have created a class you need to register it in service container with tag `modera_backend_tools_settings.contributions.sections_provider`.

    <service id="mycompany_blog.contributions.settings_sections_provider"
             class="MyCompany\BlogBundle\Contributions\SettingsSectionsProvider">

        <tag name="modera_backend_tools_settings.contributions.sections_provider" />
    </service>

Now if you go to "Backend / Tools / Settings" you should see a section there with name "Blog", it url it will be
named as "blog", icon will be "gear" ( see FontAwesome library ) and `Modera.backend.dcab.runtime.SettingsListActivity`
javascript activity will be used to create its UI.

## Licensing

This bundle is under the MIT license. See the complete license in the bundle:
Resources/meta/LICENSE