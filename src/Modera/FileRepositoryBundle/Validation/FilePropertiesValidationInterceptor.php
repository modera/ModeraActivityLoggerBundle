<?php

namespace Modera\FileRepositoryBundle\Validation;

use Modera\FileRepositoryBundle\Entity\Repository;
use Modera\FileRepositoryBundle\Entity\StoredFile;
use Modera\FileRepositoryBundle\Exceptions\FileValidationException;
use Modera\FileRepositoryBundle\Intercepting\OperationInterceptorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * This interceptor makes it possible to validate an uploaded file using these configuration
 * properties:
 * - images_only
 * - max_size
 * - use_file_constraint
 * - use_image_constraint.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2015 Modera Foundation
 */
class FilePropertiesValidationInterceptor implements OperationInterceptorInterface
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function beforePut(\SplFileInfo $file, Repository $repository)
    {
        $config = $repository->getConfig();

        $wrapper = new FileWrapper($file);
        if (isset($config['images_only']) && true === $config['images_only']) {
            $wrapper->addImageConstraint();
        }
        if (isset($config['max_size']) && '' != $config['max_size']) {
            $wrapper->addFileConstraint(array(
                'maxSize' => $config['max_size'],
            ));
        }

        if (isset($config['file_constraint']) && is_array($config['file_constraint'])) {
            $wrapper->addFileConstraint($config['file_constraint']);
        }
        if (isset($config['image_constraint']) && is_array($config['image_constraint'])) {
            $wrapper->addImageConstraint($config['image_constraint']);
        }

        $errors = $wrapper->validate($this->validator);

        if (count($errors)) {
            throw FileValidationException::create($file, $errors, $repository);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function onPut(StoredFile $storedFile, \SplFileInfo $file, Repository $repository)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function afterPut(StoredFile $storedFile, \SplFileInfo $file, Repository $repository)
    {
    }
}
