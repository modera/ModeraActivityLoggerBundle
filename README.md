# ModeraFileUploaderBundle

[![Build Status](https://travis-ci.org/modera/ModeraFileUploaderBundle.svg?branch=master)](https://travis-ci.org/modera/ModeraFileUploaderBundle)
[![StyleCI](https://styleci.io/repos/29134461/shield)](https://styleci.io/repos/29134461)

The bundle simplifies and introduces a consistent approach to uploading and storing uploaded files.

## Installation

Add this dependency to your composer.json:

    "modera/file-uploader-bundle": "~1.0"

Update your AppKernel class and add these bundles there:

    new Modera\FileRepositoryBundle\ModeraFileRepositoryBundle(), // if you still don't have it
    new Modera\FileUploaderBundle\ModeraFileUploaderBundle()

Update your routing.yml file and add these lines there:

    modera_file_uploader:
        resource: "@ModeraFileUploaderBundle/Controller/"
        type:     annotation
        prefix:   /

Update your app/config/config.yml and enable the uploader:

    modera_file_uploader:
        is_enabled: true

## Documentation

Before you can upload files you need to create a repository that will host them, for instructions please
see [ModeraFileRepositoryBundle](https://github.com/modera/ModeraFileRepositoryBundle).

Once you have a repository configured, from web you can send request with files to uploader gateway URL ( configured by
modera_file_uploader/url configuration property, default value is `uploader-gateway` ) and it will upload them and
put to a configured repository. For example, javascript pseudo code:

    filesForm.submit({
        url: 'uploader-gateway',
        params: {
            _repository: 'my_files'
        }
    });

Request parameter `_repository` will be used to determine what repository to use to store uploaded files. By default
all repositories are exposed to web and files can be uploaded to them, this feature is controller by `expose_all_repositories`
configuration property.

## Licensing

This bundle is under the MIT license. See the complete license in the bundle:
Resources/meta/LICENSE