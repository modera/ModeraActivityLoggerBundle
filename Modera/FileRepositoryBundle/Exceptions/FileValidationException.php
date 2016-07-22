<?php

namespace Modera\FileRepositoryBundle\Exceptions;

use Modera\FileRepositoryBundle\Entity\Repository;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2015 Modera Foundation
 */
class FileValidationException extends \RuntimeException
{
    /**
     * @var \SplFileInfo
     */
    private $validatedFile;

    /**
     * @var ConstraintViolationListInterface
     */
    private $errors;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @param \SplFileInfo                                                          $validatedFile
     * @param ConstraintViolationListInterface|ConstraintViolationInterface[]|array $errors
     * @param Repository|null                                                       $repository
     *
     * @return FileValidationException
     */
    public static function create(\SplFileInfo $validatedFile, $errors, Repository $repository = null)
    {
        $parsedErrors = array();
        foreach ($errors as $error) {
            if ($error instanceof ConstraintViolationInterface) {
                $parsedErrors[] = $error->getMessage();
            } else {
                $parsedErrors[] = (string) $error;
            }
        }

        $me = new static('File validation failed: '.implode(', ', $parsedErrors));
        $me->validatedFile = $validatedFile;
        $me->errors = $errors;
        $me->repository = $repository;

        return $me;
    }

    /**
     * @return \SplFileInfo
     */
    public function getValidatedFile()
    {
        return $this->validatedFile;
    }

    /**
     * @return ConstraintViolationListInterface
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }
}
