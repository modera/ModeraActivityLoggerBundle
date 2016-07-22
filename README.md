# ModeraMJRCacheAwareClassLoaderBundle

[![Build Status](https://travis-ci.org/modera/ModeraMJRCacheAwareClassLoaderBundle.svg?branch=master)](https://travis-ci.org/modera/ModeraMJRCacheAwareClassLoaderBundle)
[![StyleCI](https://styleci.io/repos/29132526/shield)](https://styleci.io/repos/29132526)

This bundles enables browser caching mechanism for MJR application, so once page is loaded then all scripts will be
permanently cached in client's browser and further page loads will not require any pre-cached scripts to be loaded
again. Shortly speaking, this is how this bundle works - it adjusts Ext.Loader class that is used to dynamically load your
scripts so it would append so called `version` number and when bundle is configured it will be suffixing all loaded
script files with that version and in conjunction with properly configured web-server ( apache/nginx instructions
provided ) next time page is loaded pre-cached scripts will be used.

## Installation

Add this dependency to your composer.json:

    "modera/mjr-cache-aware-class-loader": "dev-master"

Update your AppKernel class and add ModeraFoundationBundle declaration there:

    new Modera\MJRCacheAwareClassLoaderBundle\ModeraMJRCacheAwareClassLoaderBundle()

Optionally you may specify a version number using bundle's semantic config, to do this you need to add this to your
`app/config/config.yml`:

    modera_mjr_cache_aware_class_loader:
        version: "1.5.0"

### Apache2

To instruct client's browser that it should use cache we will need to have `mod_expires` apache module installed. On Debian
like system this can be done by issuing these commands:

    $ sudo a2enmod expires
    $ sudo service apache2 restart

Once the module is enabled you can take `Resources/server/.htaccess` file shipped with this bundle and put it to
your web directory. Feel free to tweak provided .htaccess, since the only real thing that you need from it is
`<IfModule mod_expires.c>` section. If caching still doesn't work make sure that support for .htaccess files is enabled
(check your virtual hosts definition in `/etc/apache2/sites-enabled/` directory or if you don't use virtual hosts
 then take a look at `/etc/apache2/apache2.conf`, please make sure that configuration property `AllowOverride` is set
 to `All` in `<Directory>` configuration section).

### Nginx

Update your virtual host ( default location on Debian-like system is `/etc/nginx/sites-enabled` ) and to your `server`
configuration section add this:

    location ~*\.js\?v=$ {
        expires 1y;
    }

And then restart nginx:

    $ sudo service restart nginx


# Documentation

Bundle provides several configuration properties that you can use to adjust it better for your needs, for
more information please see `\Modera\MJRCacheAwareClassLoaderBundle\DependencyInjection\Configuration`.

## Licensing

This bundle is under the MIT license. See the complete license in the bundle:
Resources/meta/LICENSE