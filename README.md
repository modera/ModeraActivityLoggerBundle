ModeraTranslationsBundle
==============

## Example of Usage

Acme/DemoBundle/Resources/config/services.xml

```

<service parent="modera_translations.handling.template_translation_handler">

    <argument>AcmeDemoBundle</argument>

    <tag name="modera_translations.translation_handler" />
</service>

<service parent="modera_translations.handling.php_classes_translation_handler">

    <argument>AcmeDemoBundle</argument>

    <tag name="modera_translations.translation_handler" />
</service>

```
