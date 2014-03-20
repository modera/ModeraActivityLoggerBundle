<?php

namespace Modera\ServerCrudBundle\Tests\Functional\Validation;

use Modera\FoundationBundle\Testing\FunctionalTestCase;
use Modera\ServerCrudBundle\Validation\DefaultEntityValidator;
use Modera\ServerCrudBundle\Validation\ValidationResult;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraints as Assert;

class DummyEntityToValidate
{
    /**
     * @Assert\NotBlank
     */
    public $id;
}

class DummyEntityToValidationWithMethod
{
    /**
     * @Assert\NotBlank
     */
    public $id;

    public $givenValidationResult;
    public $givenContainer;

    public function validateIt(ValidationResult $validationResult, ContainerInterface $container)
    {
        $this->givenValidationResult = $validationResult;
        $this->givenContainer = $container;

        $validationResult->addGeneralError('an error');
    }
}

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class DefaultEntityValidatorTest extends FunctionalTestCase
{
    /* @var DefaultEntityValidator */
    private $validator;

    // override
    public function doSetUp()
    {
        $this->validator = self::$container->get('modera_server_crud.validation.default_entity_validator');
    }

    public function testIfServiceExists()
    {
        $this->assertInstanceOf(DefaultEntityValidator::clazz(), $this->validator);
    }

    public function testValidateBySymfonyServices()
    {
        $entity = new DummyEntityToValidate();

        $config = array(
            'entity_validation_method' => 'validateIt',
            'ignore_standard_validator' => false
        );

        $result = $this->validator->validate($entity, $config);

        $this->assertInstanceOf(ValidationResult::clazz(), $result);
        $this->assertTrue($result->hasErrors());

        $fieldErrors = $result->getFieldErrors('id');

        $this->assertTrue(is_array($fieldErrors));
        $this->assertEquals(1, count($fieldErrors));
        $this->assertEquals('This value should not be blank.', $fieldErrors[0]);
    }

    public function testValidateWithEntityMethodOnly()
    {
        $entity = new DummyEntityToValidationWithMethod();

        $config = array(
            'entity_validation_method' => 'validateIt',
            'ignore_standard_validator' => true
        );

        $result = $this->validator->validate($entity, $config);

        $this->assertInstanceOf(ValidationResult::clazz(), $result);
        $this->assertTrue($result->hasErrors());
        $this->assertTrue(in_array('an error', $result->getGeneralErrors()));
        $this->assertInstanceOf(ValidationResult::clazz(), $entity->givenValidationResult);
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerInterface', $entity->givenContainer);
    }

    public function testValidateBoth()
    {
        $entity = new DummyEntityToValidationWithMethod();

        $config = array(
            'entity_validation_method' => 'validateIt',
            'ignore_standard_validator' => false
        );

        $result = $this->validator->validate($entity, $config);

        $this->assertInstanceOf(ValidationResult::clazz(), $result);
        $this->assertTrue($result->hasErrors());
        $this->assertTrue(in_array('an error', $result->getGeneralErrors()));
        $this->assertInstanceOf(ValidationResult::clazz(), $entity->givenValidationResult);
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerInterface', $entity->givenContainer);

        $this->assertEquals(1, count($result->getFieldErrors('id')));
    }
}