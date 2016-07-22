<?php

namespace Modera\FileRepositoryBundle\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Simplifies file validation using native Symfony constraints.
 *
 * @link http://symfony.com/doc/current/book/validation.html#file-constraints
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2015 Modera Foundation
 */
class FileWrapper
{
    /**
     * @var \SplFileInfo
     */
    protected $file;

    /**
     * @var array
     */
    protected static $constraints = array();

    /**
     * @param \SplFileInfo $file        A file that is being uploaded to a repository.
     * @param array        $constraints Instances of \Symfony\Component\Validator\Constraint.
     */
    public function __construct(\SplFileInfo $file, array $constraints = array())
    {
        $this->file = $file;
        self::$constraints = $constraints;
    }

    /**
     * @return \SplFileInfo
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return Constraint[]
     */
    public function getConstraints()
    {
        return self::$constraints;
    }

    /**
     * Adds an Image constraint.
     *
     * @link http://symfony.com/doc/current/reference/constraints/File.html
     *
     * @param array $options
     */
    public function addImageConstraint(array $options = array())
    {
        self::$constraints[] = new Assert\Image($options);
    }

    /**
     * Adds a File constraint.
     *
     * @link http://symfony.com/doc/current/reference/constraints/Image.html
     *
     * @param array $options
     */
    public function addFileConstraint(array $options = array())
    {
        self::$constraints[] = new Assert\File($options);
    }

    /**
     * @param ValidatorInterface $validator
     *
     * @return \Symfony\Component\Validator\ConstraintViolationListInterface
     */
    public function validate(ValidatorInterface $validator)
    {
        return $validator->validate($this);
    }

    /**
     * @private
     *
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        foreach (self::$constraints as $constraint) {
            $metadata->addPropertyConstraint('file', $constraint);
        }
    }
}
