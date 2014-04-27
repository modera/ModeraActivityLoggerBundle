# ModeraFileRepositoryBundle

This bundle provides a high level API for putting your files to virtual file repositories which internally
use Gaufrette filesystem abstraction layer.

This bundle proves useful when you need to have a consistent way of storing your files with an ability
to later reference these files in your domain model or query them ( using Doctrine ORM ). Configuration
process consists of two steps:

 * Configuring Gaufrette filesystem adapter
 * Creating a virtual repository

This is sample filesystem configuration using Gaufrette:

    knp_gaufrette:
        adapters:
            local_fs:
                local:
                    directory: /path/to/my/filesystem
        filesystems:
            local_fs:
                adapter:    local_fs

Once low-level filesystem is configured you can create a repository that will manage your files:

    /* @var \Modera\FileRepositoryBundle\Repository\FileRepository $fr */
    $fr = $container->get('modera_file_repository.repository.file_repository');

    $repositoryConfig = array(
        'filesystem' => 'local_fs'
    );

    $fr->createRepository('my_repository', $repositoryConfig, 'My dummy repository');

    $dummyFile = new \SplFileInfo('dummy-file.txt');

    /* @var \Modera\FileRepositoryBundle\Entity\StoredFile $storedFile */
    $storedFile = $fs->put('my_repository', $dummyFile);

When a physical file is put to a repository its descriptor record is created in database that later you can use
in your domain logic. For example, having a Doctrine entity which represents a physical may prove useful when you
have a user and you want to associate a profile picture with that user. Also it is worth mentioning that once StoredFile
entity is removed, its physical file stored in a configured filesystem will be automatically removed as well. This
descriptive record saved in database contains a bunch of useful information like mime-type, file extension etc, please
see StoredFile's entity fields for more details.

## Repository configuration

When you create a repository you can use these configuration properties to tweak behaviour of your repository:

 * filesystem -- Gaufrette's filesystem name that this repository should use to store files
 * storage_key_generator -- DI service ID of class which implements `Modera\FileRepositoryBundle\Repository\StorageKeyGeneratorInterface`
                            interface. This class is used to generate filenames that will be used by filesystem to store
                            files. If this configuration property is not provided when repository is created then
                            `Modera\FileRepositoryBundle\Repository\UniqidKeyGenerator` class will be used.


